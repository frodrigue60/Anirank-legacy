<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tournament;
use App\Models\TournamentMatchup;

class ProcessTournaments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tournaments:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process active tournaments and advance rounds when matchups expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting tournament processing...');

        $matchups = TournamentMatchup::where('is_active', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->whereHas('tournament', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        $this->info("Found {$matchups->count()} expired active matchups.");

        foreach ($matchups as $matchup) {
            $this->processMatchup($matchup);
        }

        $tournamentIds = $matchups->pluck('tournament_id')->unique();
        foreach ($tournamentIds as $id) {
            $t = Tournament::find($id);
            if ($t && $t->status === 'active') {
                $activeRound = $t->matchups()->where('is_active', true)->min('round');
                if ($activeRound && $activeRound != $t->current_round) {
                    $t->current_round = $activeRound;
                    $t->save();
                }
            }
        }

        $this->info('Tournament processing completed.');
    }

    protected function processMatchup(TournamentMatchup $matchup)
    {
        $matchup->is_active = false;

        // Determine Winner
        if ($matchup->song1_votes > $matchup->song2_votes) {
            $winnerId = $matchup->song1_id;
        } elseif ($matchup->song2_votes > $matchup->song1_votes) {
            $winnerId = $matchup->song2_id;
        } else {
            // Tie-breaker: random fallback
            $winnerId = rand(0, 1) ? $matchup->song1_id : $matchup->song2_id;
        }

        // Handle case where a "bye" happened (song2 is null)
        if (! $matchup->song2_id && $matchup->song1_id) {
            $winnerId = $matchup->song1_id;
        } elseif (! $matchup->song1_id && $matchup->song2_id) {
             $winnerId = $matchup->song2_id;
        }

        $matchup->winner_song_id = $winnerId;
        $matchup->save();

        $tournament = $matchup->tournament;

        if ($matchup->round == 2) {
            // It's the final!
            $tournament->status = 'completed';
            $tournament->completed_at = now();
            $tournament->winner_song_id = $winnerId;
            $tournament->save();
            $this->info("Tournament {$tournament->id} completed. Winner: Song {$winnerId}");
            return;
        }

        // Advance to next round
        $nextRound = $matchup->round / 2;
        $nextPosition = ceil($matchup->position / 2);

        $nextMatchup = TournamentMatchup::firstOrCreate([
            'tournament_id' => $tournament->id,
            'round' => $nextRound,
            'position' => $nextPosition,
        ]);

        if ($matchup->position % 2 != 0) {
            $nextMatchup->song1_id = $winnerId;
        } else {
            $nextMatchup->song2_id = $winnerId;
        }

        if ($nextMatchup->song1_id && $nextMatchup->song2_id) {
            $nextMatchup->is_active = true;
            if (!$nextMatchup->ends_at) {
                $nextMatchup->ends_at = now()->addDays(2);
            }
        }

        $nextMatchup->save();

        $this->info("Advanced Winner (Song {$winnerId}) to Round {$nextRound} Position {$nextPosition} for Tournament {$tournament->id}");
    }
}
