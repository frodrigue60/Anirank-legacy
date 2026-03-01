<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tournament;

class TournamentBracket extends Component
{
    public Tournament $tournament;

    public function mount(Tournament $tournament)
    {
        $this->tournament = $tournament;
    }

    public function render()
    {
        // Load the matchups grouped by round
        $this->tournament->load(['matchups.song1', 'matchups.song2', 'matchups.winner', 'winner']);
        
        // Group by round. Sort descending so 16 comes before 8, etc.
        $rounds = $this->tournament->matchups->groupBy('round')->sortKeysDesc();

        return view('livewire.tournament-bracket', compact('rounds'));
    }
}
