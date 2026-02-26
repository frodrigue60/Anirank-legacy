<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Anime;
use App\Models\Season;
use App\Models\Year;
use App\Models\Format;
use App\Models\Genre;

class AnimesTable extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $name = '';
    
    #[Url(except: '')]
    public $year_id = '';
    
    #[Url(except: '')]
    public $season_id = '';
    
    #[Url(except: '')]
    public $format_id = '';

    #[Url(except: '')]
    public $genre_id = '';

    #[Url(except: 'grid_small')]
    public $viewMode = 'grid_small';
    
    public $perPage = 15;
    public $hasMorePages = false;
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function mount()
    {
        $this->viewMode = 'grid_small';
    }

    public function updatedName()
    {
        $this->resetPage();
        $this->perPage = 15;
    }
    
    public function updatedYearId()
    {
        $this->resetPage();
        $this->perPage = 15;
    }
    
    public function updatedSeasonId()
    {
        $this->resetPage();
        $this->perPage = 15;
    }
    
    public function updatedFormatId()
    {
        $this->resetPage();
        $this->perPage = 15;
    }

    public function updatedGenreId()
    {
        $this->resetPage();
        $this->perPage = 15;
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function loadMore()
    {
        if ($this->hasMorePages && $this->readyToLoad) {
            $this->perPage += 15;
        }
    }

    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.animes-table', [
                'animes' => collect(),
                'years' => collect(),
                'seasons' => collect(),
                'formats' => collect(),
                'all_genres' => collect(),
            ]);
        }

        $query = Anime::where('status', true);

        if ($this->name) {
            $query->where('title', 'LIKE', '%' . $this->name . '%');
        }
        if ($this->year_id) {
            $query->where('year_id', $this->year_id);
        }
        if ($this->season_id) {
            $query->where('season_id', $this->season_id);
        }
        if ($this->format_id) {
            $query->where('format_id', $this->format_id);
        }
        if ($this->genre_id) {
            $query->whereHas('genres', function ($q) {
                $q->where('genres.id', $this->genre_id);
            });
        }

        $results = $query->with(['format:id,name', 'season:id,name', 'year:id,name', 'studios:id,name,slug', 'genres:id,name'])
            ->withCount('songs')
            ->orderBy('title')
            ->orderBy('id', 'asc')
            ->take($this->perPage + 1)
            ->get();

        $this->hasMorePages = $results->count() > $this->perPage;
        $animes = $results->take($this->perPage);

        $years = Year::orderBy('name', 'desc')->get(['id', 'name']);
        $seasons = Season::all(['id', 'name']);
        $formats = Format::all(['id', 'name']);
        $all_genres = Genre::orderBy('name')->get(['id', 'name']);

        return view('livewire.animes-table', [
            'animes' => $animes,
            'years' => $years,
            'seasons' => $seasons,
            'formats' => $formats,
            'all_genres' => $all_genres,
        ]);
    }
}
