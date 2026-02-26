<?php

namespace App\Console\Commands;

use App\Models\RankingHistory;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TrackDailyRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:track-ranking {--seasonal-only : Only recalculate seasonal rankings}';

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
        $seasonalOnly = $this->option('seasonal-only');
        $this->info($seasonalOnly ? 'Starting seasonal-only ranking tracking...' : 'Starting ranking tracking...');

        $date = now()->toDateString();

        $rankings = [];

        // 1. Calculate Global Rankings (skip if --seasonal-only)
        if (! $seasonalOnly) {
            $allSongs = Song::query()
                ->withAvg('ratings', 'rating')
                ->whereHas('anime', function ($query) {
                    $query->where('status', true);
                })
                ->orderByDesc('ratings_avg_rating')
                ->get();

            $this->info("Found {$allSongs->count()} total active songs for global ranking.");

            foreach ($allSongs as $index => $song) {
                $rankings[$song->id] = [
                    'rank' => $index + 1,
                    'score' => $song->ratings_avg_rating ?? 0,
                ];
            }
        } else {
            // Pre-populate rankings map with existing global data so we can attach seasonal
            $allSongs = Song::query()
                ->withAvg('ratings', 'rating')
                ->whereHas('anime', fn ($q) => $q->where('status', true))
                ->get();

            foreach ($allSongs as $song) {
                $rankings[$song->id] = [
                    'score' => $song->ratings_avg_rating ?? 0,
                ];
            }
        }

        // 2. Calculate Seasonal Rankings
        // Get unique season/year pairs from active songs
        $seasonalPairs = DB::table('songs')
            ->join('animes', 'songs.anime_id', '=', 'animes.id')
            ->where('animes.status', true)
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
                ->whereHas('anime', function ($query) {
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
            $updateData = $seasonalOnly
                ? ['seasonal_rank' => $data['seasonal_rank'] ?? null, 'score' => $data['score']]
                : ['rank' => $data['rank'], 'seasonal_rank' => $data['seasonal_rank'] ?? null, 'score' => $data['score']];

            RankingHistory::updateOrCreate(
                [
                    'song_id' => $songId,
                    'date' => $date,
                ],
                $updateData
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Ranking history updated successfully for '.$date);

        return Command::SUCCESS;
    }
}
