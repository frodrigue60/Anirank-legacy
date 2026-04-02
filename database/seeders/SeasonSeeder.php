<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Season;
use Illuminate\Support\Str;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            'Winter',
            'Spring',
            'Summer',
            'Fall',
        ];

        $currentMonth = (int) date('n');
        $currentSeasonName = match (true) {
            $currentMonth >= 1  && $currentMonth <= 3  => 'Winter',
            $currentMonth >= 4  && $currentMonth <= 6  => 'Spring',
            $currentMonth >= 7  && $currentMonth <= 9  => 'Summer',
            $currentMonth >= 10 && $currentMonth <= 12 => 'Fall',
            default => null,
        };

        // Reset all current flags to false first
        Season::query()->update(['current' => false]);

        Season::withoutEvents(function () use ($seasons, $currentSeasonName) {
            foreach ($seasons as $season) {
                Season::updateOrCreate(
                    ['slug' => Str::slug($season)],
                    [
                        'name' => $season,
                        'current' => ($season === $currentSeasonName),
                    ]
                );
            }
        });
    }
}
