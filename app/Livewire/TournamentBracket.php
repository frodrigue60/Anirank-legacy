<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tournament;

class TournamentBracket extends Component
{
    public Tournament $tournament;

    public $viewMode = 'list'; // 'list' or 'tree'
    
    // For List View pagination
    public $activeRound = null;
    public $activeGroup = 'A';

    public function mount(Tournament $tournament)
    {
        $this->tournament = $tournament;
        
        // Default to the first round (largest number)
        if ($this->tournament->matchups()->count() > 0) {
            $this->activeRound = $this->tournament->matchups()->max('round');
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function setRound($round)
    {
        $this->activeRound = $round;
        $this->activeGroup = 'A'; // Reset group when changing rounds
    }

    public function setGroup($group)
    {
        $this->activeGroup = $group;
    }

    public function render()
    {
        $this->tournament->load(['matchups.song1', 'matchups.song2', 'matchups.winner', 'winner']);
        
        $rounds = collect();
        $groups = [];
        $activeMatchups = collect();

        if ($this->tournament->matchups->isNotEmpty()) {
            // Group by round. Sort descending so 16 comes before 8, etc.
            $rounds = $this->tournament->matchups->groupBy('round')->sortKeysDesc();

            if ($this->viewMode === 'list' && $this->activeRound) {
                // Get all matchups for the active round
                $roundMatchups = $rounds->get($this->activeRound, collect())->sortBy('position')->values();
                
                // If there are many matchups (e.g. 16 or 32 forming 8 or 16 pairs), split into groups
                // We'll define a group size of 4 matchups max per group for readability in early rounds
                if ($roundMatchups->count() > 4) {
                    $chunks = $roundMatchups->chunk(4);
                    // Lettering array A, B, C, D...
                    $letters = range('A', 'Z');
                    
                    foreach ($chunks as $index => $chunk) {
                        $groups[$letters[$index]] = $chunk;
                    }
                    
                    // Fallback to A if activeGroup somehow became invalid
                    if (!isset($groups[$this->activeGroup])) {
                        $this->activeGroup = 'A';
                    }
                    
                    $activeMatchups = $groups[$this->activeGroup] ?? collect();
                } else {
                    // No need for groups if 4 or fewer matchups in the round
                    $activeMatchups = $roundMatchups;
                }
            }
        }

        return view('livewire.tournament-bracket', [
            'rounds' => $rounds,
            'groups' => $groups,
            'activeMatchups' => $activeMatchups
        ]);
    }
}
