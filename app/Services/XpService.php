<?php

namespace App\Services;

use App\Models\User;
use App\Models\XpActivity;
use App\Models\XpLog;
use App\Models\Level;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class XpService
{
    /**
     * Award XP to a user based on an activity key.
     *
     * @param User $user
     * @param string $key
     * @param array $metadata
     * @return bool
     */
    public function award(User $user, string $key, array $metadata = []): bool
    {
        $activity = XpActivity::where('key', $key)->first();

        if (!$activity) {
            return false;
        }

        if (!$this->canAward($user, $activity, $metadata)) {
            return false;
        }

        return DB::transaction(function () use ($user, $activity, $metadata) {
            // Create log
            XpLog::create([
                'user_id' => $user->id,
                'xp_activity_id' => $activity->id,
                'xp_amount' => $activity->xp_amount,
                'metadata' => $metadata,
            ]);

            // Update user XP
            $user->increment('xp', $activity->xp_amount);

            // Check level up
            $this->checkLevelUp($user);

            return true;
        });
    }

    /**
     * Check if XP can be awarded (cooldown checks).
     *
     * @param User $user
     * @param XpActivity $activity
     * @param array $metadata
     * @return bool
     */
    public function canAward(User $user, XpActivity $activity, array $metadata): bool
    {
        // 1. Check for unique activities (only awarded once)
        if ($this->isUniqueActivity($activity->key)) {
            $query = XpLog::where('user_id', $user->id)
                ->where('xp_activity_id', $activity->id);

            // Check metadata matches for uniqueness (e.g., same song_id)
            foreach ($metadata as $key => $value) {
                $query->whereJsonContains("metadata->$key", $value);
            }

            if ($query->exists()) {
                return false;
            }
        }

        // 2. Check for cooldowns
        if (!$activity->cooldown_seconds) {
            return true;
        }

        $lastLog = XpLog::where('user_id', $user->id)
            ->where('xp_activity_id', $activity->id)
            ->latest()
            ->first();

        if (!$lastLog) {
            return true;
        }

        $cooldownEnd = $lastLog->created_at->addSeconds($activity->cooldown_seconds);
        
        return Carbon::now()->greaterThan($cooldownEnd);
    }

    /**
     * Determine if an activity should only be awarded once per target.
     */
    protected function isUniqueActivity(string $key): bool
    {
        return in_array($key, ['add_favorite', 'create_playlist', 'add_to_playlist']);
    }

    /**
     * Check if the user reached a new level.
     *
     * @param User $user
     * @return void
     */
    public function checkLevelUp(User $user): void
    {
        $newLevel = Level::where('min_xp', '<=', $user->xp)
            ->orderBy('level', 'desc')
            ->first();

        $levelValue = $newLevel ? $newLevel->level : 1;

        if ($levelValue != $user->level) {
            $user->update(['level' => $levelValue]);
        }
    }

    /**
     * Synchronize a user's XP and Level based on their logs.
     */
    public function syncUser(User $user): array
    {
        $oldXp = $user->xp;
        $oldLevel = $user->level;

        $actualXp = XpLog::where('user_id', $user->id)->sum('xp_amount');
        
        $user->xp = $actualXp;
        $this->checkLevelUp($user);
        $user->save();

        return [
            'user_id' => $user->id,
            'old_xp' => $oldXp,
            'new_xp' => $actualXp,
            'old_level' => $oldLevel,
            'new_level' => $user->level,
            'changed' => ($oldXp != $actualXp || $oldLevel != $user->level)
        ];
    }

    /**
     * Synchronize all users.
     */
    public function syncAll(): array
    {
        $results = [];
        User::chunk(100, function ($users) use (&$results) {
            foreach ($users as $user) {
                $res = $this->syncUser($user);
                if ($res['changed']) {
                    $results[] = $res;
                }
            }
        });

        return $results;
    }
}
