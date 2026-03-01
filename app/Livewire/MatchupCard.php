<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TournamentMatchup;
use App\Models\TournamentVote;
use Illuminate\Support\Facades\DB;

class MatchupCard extends Component
{
    public TournamentMatchup $matchup;
    public bool $hasVoted = false;

    public function mount(TournamentMatchup $matchup)
    {
        $this->matchup = $matchup;
        $this->checkIfVoted();
    }

    public function checkIfVoted()
    {
        if (auth()->check()) {
            $this->hasVoted = TournamentVote::where('tournament_matchup_id', $this->matchup->id)
                ->where('user_id', auth()->id())
                ->exists();
        }
    }

    public function vote($songId)
    {
        if (!auth()->check()) {
            $this->dispatch('toast', type: 'error', message: 'You must be logged in to vote.');
            return;
        }

        if (!$this->matchup->is_active) {
            $this->dispatch('toast', type: 'error', message: 'This round has ended.');
            return;
        }

        if ($this->hasVoted) {
            $this->dispatch('toast', type: 'error', message: 'You have already voted in this matchup.');
            return;
        }

        try {
            DB::transaction(function () use ($songId) {
                // Insert vote
                TournamentVote::create([
                    'tournament_matchup_id' => $this->matchup->id,
                    'user_id' => auth()->id(),
                    'song_id' => $songId,
                    'ip_address' => request()->ip()
                ]);

                // Increment counter
                if ($this->matchup->song1_id == $songId) {
                    $this->matchup->increment('song1_votes');
                } elseif ($this->matchup->song2_id == $songId) {
                    $this->matchup->increment('song2_votes');
                }
            });

            $this->hasVoted = true;
            $this->matchup->refresh();
            
            $this->dispatch('toast', type: 'success', message: 'Vote correctly recorded!');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'An error occurred while voting. You might have already voted.');
        }
    }

    public function render()
    {
        return view('livewire.matchup-card');
    }
}
