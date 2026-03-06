<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Song;
use App\Models\SongVariant;

#[Lazy]
class ActivityFeed extends Component
{
    public function render()
    {
        $activities = \App\Models\Activity::with(['user', 'target'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Load missing relations for polymorphic targets
        $activities->each(function ($act) {
            if ($act->target instanceof \App\Models\Song) {
                $act->target->loadMissing('anime');
            } elseif ($act->target instanceof \App\Models\SongVariant) {
                $act->target->loadMissing('song.anime');
            }
        });

        $activities = $activities->filter(function ($act) {
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
