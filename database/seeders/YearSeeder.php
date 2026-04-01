<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Year;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startYear = 2000;
        $endYear = (int) date('Y') + 1; // Until next year
        $currentYear = (int) date('Y');

        // Reset all current flags to false first to ensure only one is current
        Year::query()->update(['current' => false]);

        Year::withoutEvents(function () use ($startYear, $endYear, $currentYear) {
            for ($year = $startYear; $year <= $endYear; $year++) {
                Year::updateOrCreate(
                    ['slug' => (string) $year],
                    [
                        'name' => (string) $year,
                        'year' => $year,
                        'current' => ($year === $currentYear),
                    ]
                );
            }
        });
    }
}
