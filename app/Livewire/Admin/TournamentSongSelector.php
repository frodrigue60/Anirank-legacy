<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tournament;
use App\Models\TournamentMatchup;
use App\Models\Song;

class TournamentSongSelector extends Component
{
    public Tournament $tournament;

    public $search = '';
    public $typeFilter = 'All'; // All, OP, ED
    
    // the list of song IDs currently selected
    public $selectedSongIds = [];
    
    // which song is currently playing in the preview
    public $previewingSongId = null;

    public function mount(Tournament $tournament)
    {
        $this->tournament = $tournament;
        
        if ($tournament->type_filter) {
            $this->typeFilter = $tournament->type_filter;
        }

        // if there are matchups already, it means the tournament isn't drafting anymore
        if ($tournament->matchups()->count() > 0) {
            return redirect()->route('admin.tournaments.show', $tournament);
        }
    }

    public function toggleSong(int $songId)
    {
        if (in_array($songId, $this->selectedSongIds)) {
            $this->selectedSongIds = array_diff($this->selectedSongIds, [$songId]);
        } else {
            if (count($this->selectedSongIds) >= $this->tournament->size) {
                // Cannot add more than the bracket size
                return;
            }
            $this->selectedSongIds[] = $songId;
        }
    }

    public function previewSong($songId)
    {
        if ($this->previewingSongId === $songId) {
            $this->previewingSongId = null; // Toggle off
        } else {
            $this->previewingSongId = $songId;
        }
    }

    public function clearAll()
    {
        $this->selectedSongIds = [];
        $this->previewingSongId = null;
    }

    public function finalizeBracket()
    {
        if (count($this->selectedSongIds) !== $this->tournament->size) {
            return back()->with('error', 'You must select exactly ' . $this->tournament->size . ' themes to finalize the bracket.');
        }

        // Create the matchups mimicking the logic in the seeder
        $shuffledSongs = collect($this->selectedSongIds)->shuffle()->values();
        $numberOfMatches = $this->tournament->size / 2;
        $round = $this->tournament->size;

        $this->tournament->current_round = $round;
        $this->tournament->status = 'active';
        $this->tournament->started_at = now();
        $this->tournament->save();

        for ($i = 0; $i < $numberOfMatches; $i++) {
            TournamentMatchup::create([
                'tournament_id' => $this->tournament->id,
                'round' => $round,
                'position' => $i + 1,
                'song1_id' => $shuffledSongs[$i * 2],
                'song2_id' => $shuffledSongs[$i * 2 + 1],
                'ends_at' => now()->addDays(2), 
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.tournaments.show', $this->tournament)->with('success', 'Bracket finalized successfully!');
    }

    public function render()
    {
        // Compute available songs based on search and type filter
        $query = Song::query()->with(['anime', 'songVariants.video']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('song_romaji', 'like', '%' . $this->search . '%')
                  ->orWhere('song_jp', 'like', '%' . $this->search . '%')
                  ->orWhere('song_en', 'like', '%' . $this->search . '%')
                  ->orWhereHas('anime', function ($qa) {
                      $qa->where('title', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->typeFilter !== 'All') {
            $query->where('type', $this->typeFilter);
        }

        // Load Top Rated by default if no search
        if (empty($this->search)) {
            $query->withCount('ratings')->orderByDesc('ratings_count');
        } else {
            // Sort by popularity but keep matched results first
            $query->orderByDesc('views');
        }

        $availableSongs = $query->take(30)->get();
        
        $selectedSongs = Song::with(['anime'])->whereIn('id', $this->selectedSongIds)->get();
        // keep them in order of addition
        $selectedSongs = $selectedSongs->sortBy(function($song) {
            return array_search($song->id, $this->selectedSongIds);
        });

        return view('livewire.admin.tournament-song-selector', [
            'availableSongs' => $availableSongs,
            'selectedSongs' => $selectedSongs,
            'totalSongsAvailable' => Song::when($this->typeFilter !== 'All', function($q) {
                $q->where('type', $this->typeFilter);
            })->count(),
        ]);
    }
}
