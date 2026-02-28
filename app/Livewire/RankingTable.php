<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

#[Lazy]
class RankingTable extends Component
{
    use Traits\HasRankingScore {
        setScoreSongs as traitSetScoreSongs;
    }

    public $currentSection = 'ALL';
    public $perPage = 15;
    public $page = 1;
    public $hasMorePages = true;

    public function placeholder()
    {
        return view('livewire.skeletons.ranking-table-skeleton');
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

    public function loadMore()
    {
        if (!$this->hasMorePages) return;
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

    #[Computed]
    public function songs()
    {

        $status = true;
        $limit = 100;
        $perPage = $this->perPage * $this->page;

        $query = Song::query()
            ->with(['anime:id,title,slug', 'artists:id,name,slug', 'previousRanking'])
            ->withAvg('ratings', 'rating')
            ->whereHas('anime', function ($query) use ($status) {
                $query->where('status', $status);
            });

        if (Auth::check()) {
            $query->with(['ratings' => function($q) {
                $q->where('user_id', Auth::id());
            }]);
        }

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
