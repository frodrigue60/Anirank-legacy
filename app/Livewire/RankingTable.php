<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class RankingTable extends Component
{
    use Traits\HasRankingScore {
        setScoreSongs as traitSetScoreSongs;
    }

    public $currentSection = 'ALL';
    public $perPage = 15;
    public $page = 1;
    public $hasMorePages = true;
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function mount()
    {
        $this->currentSection = 'ALL';
    }

    public function updatedCurrentSection()
    {
        $this->page = 1;
        $this->hasMorePages = true;
    }

    #[On('loadMore')]
    public function loadMore()
    {
        if (!$this->hasMorePages || !$this->readyToLoad) return;
        $this->page++;
    }

    public function toggleFavorite($songId)
    {
        if (!Auth::check()) {
            return $this->dispatch('showLoginModal');
        }

        $song = Song::find($songId);
        if ($song) {
            $song->toggleFavorite();
        }
    }

    public function getSongsProperty()
    {
        if (!$this->readyToLoad) return collect();

        $status = true;
        $limit = 100;
        $perPage = $this->perPage * $this->page;

        $query = Song::query()
            ->with(['post:id,title,slug', 'artists:id,name,slug'])
            ->withAvg('ratings', 'rating')
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            });

        if ($this->currentSection !== 'ALL') {
            $query->where('type', $this->currentSection);
        }

        $query->orderByDesc('ratings_avg_rating');

        $songs = $query->take(min($perPage, $limit))->get();

        $this->hasMorePages = $songs->count() >= $perPage && $songs->count() < $limit;

        return $this->setScoreSongs($songs, Auth::user());
    }

    public function setScoreSongs($songs, $user)
    {
        // Call the trait's method to calculate scores
        $this->traitSetScoreSongs($songs, $user);

        foreach ($songs as $index => $song) {
            $song->current_rank = ($this->page - 1) * $this->perPage + $index + 1;
            $song->previous_rank = $song->getPreviousRank();

            if ($song->previous_rank) {
                if ($song->current_rank < $song->previous_rank) {
                    $song->trend = 'UP';
                } elseif ($song->current_rank > $song->previous_rank) {
                    $song->trend = 'DOWN';
                } else {
                    $song->trend = 'SAME';
                }
            } else {
                $song->trend = 'NEW';
            }
        }

        return $songs;
    }

    public function render()
    {
        return view('livewire.ranking-table', [
            'songs' => $this->songs,
        ]);
    }
}
