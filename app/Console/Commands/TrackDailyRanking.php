<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Song;
use App\Models\RankingHistory;
use Illuminate\Support\Facades\DB;

class TrackDailyRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:track-ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store daily ranking history for all songs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting ranking tracking...');

        $date = now()->toDateString();

        // 1. Calculate Global Rankings
        $allSongs = Song::query()
            ->withAvg('ratings', 'rating')
            ->whereHas('post', function ($query) {
                $query->where('status', true);
            })
            ->orderByDesc('ratings_avg_rating')
            ->get();

        $this->info("Found {$allSongs->count()} total active songs for global ranking.");

        // We prepare a map to bulk update/create or just a way to store results
        $rankings = [];

        foreach ($allSongs as $index => $song) {
            $rankings[$song->id] = [
                'rank' => $index + 1,
                'score' => $song->ratings_avg_rating ?? 0,
            ];
        }

        // 2. Calculate Seasonal Rankings
        // Get unique season/year pairs from active songs
        $seasonalPairs = DB::table('songs')
            ->join('posts', 'songs.post_id', '=', 'posts.id')
            ->where('posts.status', true)
            ->whereNotNull('songs.season_id')
            ->whereNotNull('songs.year_id')
            ->select('songs.season_id', 'songs.year_id')
            ->distinct()
            ->get();

        $this->info("Found {$seasonalPairs->count()} unique season-year pairs.");

        foreach ($seasonalPairs as $pair) {
            $seasonalSongs = Song::query()
                ->withAvg('ratings', 'rating')
                ->where('season_id', $pair->season_id)
                ->where('year_id', $pair->year_id)
                ->whereHas('post', function ($query) {
                    $query->where('status', true);
                })
                ->orderByDesc('ratings_avg_rating')
                ->get();

            foreach ($seasonalSongs as $index => $song) {
                if (isset($rankings[$song->id])) {
                    $rankings[$song->id]['seasonal_rank'] = $index + 1;
                }
            }
        }

        // 3. Persist to Database
        $bar = $this->output->createProgressBar(count($rankings));
        $bar->start();

        foreach ($rankings as $songId => $data) {
            RankingHistory::updateOrCreate(
                [
                    'song_id' => $songId,
                    'date' => $date
                ],
                [
                    'rank' => $data['rank'],
                    'seasonal_rank' => $data['seasonal_rank'] ?? null,
                    'score' => $data['score']
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Ranking history updated successfully for ' . $date);

        return Command::SUCCESS;
    }
}
