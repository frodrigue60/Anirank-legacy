<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use App\Models\Song;
use App\Models\Season;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Lazy]
class SeasonalTable extends Component
{
    use WithPagination;
    use Traits\HasRankingScore {
        setScoreSongs as traitSetScoreSongs;
    }

    public $currentSection = 'ALL';
    public $perPage = 15;
    public $page = 1;
    public $hasMorePages = true;
    public $seasonId;
    public $yearId;
    public $seasonName;
    public $yearName;

    public function placeholder()
    {
        return view('livewire.skeletons.seasonal-table-skeleton');
    }

    public function mount($season, $year)
    {
        $this->seasonId = $season;
        $this->yearId = $year;
        $this->seasonName = Season::find($season)?->name;
        $this->yearName = Year::find($year)?->name;
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
            ->with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->withAvg('ratings', 'rating')
            ->whereHas('anime', function ($query) use ($status) {
                $query->where('status', $status)
                    ->where('season_id', $this->seasonId)
                    ->where('year_id', $this->yearId);
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
        // Use the trait's method to calculate scores
        $this->traitSetScoreSongs($songs, $user);

        foreach ($songs as $index => $song) {
            $song->current_rank = ($this->page - 1) * $this->perPage + $index + 1;
            $song->previous_rank = $song->getPreviousSeasonalRank();

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
        return view('livewire.seasonal-table', [
            'songs' => $this->songs,
        ]);
    }
}
