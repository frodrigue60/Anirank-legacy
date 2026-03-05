<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Song;
use App\Models\SongVariant;

#[Lazy]
class ActivityFeed extends Component
{
    public function render()
    {
        // 1. Fetch the raw timeline using UNION ALL
        // We select a common schema: id, user_id, type, target_id, target_type, value (optional), created_at
        
        $favorites = DB::table('favorites')
            ->select('id', 'user_id', DB::raw("'favorite' as action_type"), 'favoritable_id as target_id', 'favoritable_type as target_type', DB::raw("NULL as action_value"), 'created_at');

        $comments = DB::table('comments')
            ->select('id', 'user_id', DB::raw("'comment' as action_type"), 'commentable_id as target_id', 'commentable_type as target_type', 'content as action_value', 'created_at');

        $ratings = DB::table('ratings')
            ->select('id', 'user_id', DB::raw("'rating' as action_type"), 'rateable_id as target_id', 'rateable_type as target_type', 'rating as action_value', 'created_at');

        $rawActivities = $favorites->unionAll($comments)
            ->unionAll($ratings)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // 2. Hydrate Models (Users)
        $userIds = $rawActivities->pluck('user_id')->unique()->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // 3. Hydrate Targets (Polymorphic: Songs, SongVariants)
        $targetMap = [];
        foreach ($rawActivities as $act) {
            $targetMap[$act->target_type][] = $act->target_id;
        }

        $hydratedTargets = [];
        foreach ($targetMap as $type => $ids) {
            $ids = array_unique($ids);
            if ($type === Song::class) {
                $hydratedTargets[$type] = Song::with('anime')->whereIn('id', $ids)->get()->keyBy('id');
            } elseif ($type === SongVariant::class) {
                $hydratedTargets[$type] = SongVariant::with(['song.anime'])->whereIn('id', $ids)->get()->keyBy('id');
            }
        }

        // 4. Attach to raw objects
        $activities = $rawActivities->map(function ($act) use ($users, $hydratedTargets) {
            $act->user = $users->get($act->user_id);
            $act->target = $hydratedTargets[$act->target_type]->get($act->target_id) ?? null;
            return $act;
        })->filter(function ($act) {
            // Filter out any orphaned actions where the target was deleted
            return $act->user !== null && $act->target !== null;
        });

        return view('livewire.activity-feed', [
            'activities' => $activities
        ]);
    }

    public function placeholder()
    {
        return view('livewire.placeholders.activity-feed-skeleton');
    }
}
