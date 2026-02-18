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

        // Fetch songs with their average rating
        // Replicating the logic from RankingTable regarding active posts and ordering
        $songs = Song::query()
            ->withAvg('ratings', 'rating')
            ->whereHas('post', function ($query) {
                $query->where('status', true);
            })
            ->orderByDesc('ratings_avg_rating')
            ->get();

        $this->info("Found {$songs->count()} active songs.");

        $bar = $this->output->createProgressBar($songs->count());
        $bar->start();

        foreach ($songs as $index => $song) {
            $rank = $index + 1;
            $score = $song->ratings_avg_rating ?? 0;

            RankingHistory::updateOrCreate(
                [
                    'song_id' => $song->id,
                    'date' => $date
                ],
                [
                    'rank' => $rank,
                    'score' => $score
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
