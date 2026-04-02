<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class XpSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initial Activities
        $activities = [
            ['key' => 'rate_song', 'xp_amount' => 160, 'description' => 'Rating a song (OP/ED)', 'cooldown_seconds' => 0],
            ['key' => 'add_favorite', 'xp_amount' => 120, 'description' => 'Adding a song or artist to favorites', 'cooldown_seconds' => 0],
            ['key' => 'comment', 'xp_amount' => 80, 'description' => 'Leaving a comment', 'cooldown_seconds' => 300],
            ['key' => 'daily_login', 'xp_amount' => 200, 'description' => 'First login of the day', 'cooldown_seconds' => 86400],
            ['key' => 'create_playlist', 'xp_amount' => 180, 'description' => 'Creating a new playlist', 'cooldown_seconds' => 300],
            ['key' => 'add_to_playlist', 'xp_amount' => 60, 'description' => 'Adding a song to a playlist', 'cooldown_seconds' => 0],
        ];

        foreach ($activities as $activity) {
            DB::table('xp_activities')->updateOrInsert(['key' => $activity['key']], $activity);
        }

        // Levels (1 to 100)
        for ($i = 1; $i <= 100; $i++) {
            DB::table('levels')->updateOrInsert(['level' => $i], [
                'min_xp' => 500 * ($i - 1) * $i,
                'name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
