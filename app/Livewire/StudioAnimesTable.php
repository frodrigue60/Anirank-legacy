<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Year;
use App\Models\Season;
use App\Models\Format;
use App\Models\Studio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class StudioAnimesTable extends Component
{
    use WithPagination;

    public $studioId;
    public $studio;

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
    
    public $perPage = 18;
    public $hasMorePages = false;
    public $readyToLoad = false;

    public function mount($studioId)
    {
        $this->studioId = $studioId;
        $this->studio = Studio::findOrFail($studioId);
    }

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function updatingName()
    {
        $this->resetPage();
    }

    public function updatingYearId()
    {
        $this->resetPage();
    }

    public function updatingSeasonId()
    {
        $this->resetPage();
    }

    public function updatingFormatId()
    {
        $this->resetPage();
    }

    public function updatingGenreId()
    {
        $this->resetPage();
    }

    public function loadMore()
    {
        if ($this->readyToLoad) {
            $this->perPage += 12;
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.studio-animes-table', [
                'posts' => collect(),
                'years' => collect(),
                'seasons' => collect(),
                'formats' => collect(),
                'all_genres' => collect(),
            ]);
        }

        $posts = Post::query()
            ->when(!Auth::check() || !Auth::user()->isStaff(), function ($query) {
                $query->where('status', true);
            })
            ->whereHas('studios', function ($query) {
                $query->where('studios.id', $this->studioId);
            })
            ->when($this->name, function ($query) {
                $query->where('title', 'like', '%' . $this->name . '%');
            })
            ->when($this->year_id, function ($query) {
                $query->where('year_id', $this->year_id);
            })
            ->when($this->season_id, function ($query) {
                $query->where('season_id', $this->season_id);
            })
            ->when($this->format_id, function ($query) {
                $query->where('format_id', $this->format_id);
            })
            ->when($this->genre_id, function ($query) {
                $query->whereHas('genres', function ($q) {
                    $q->where('genres.id', $this->genre_id);
                });
            })
            ->with([
                'format:id,name',
                'season:id,name',
                'year:id,name',
                'studios:id,name,slug',
                'producers:id,name,slug',
                'genres:id,name'
            ])
            ->orderBy('title', 'asc')
            ->paginate($this->perPage);

        $this->hasMorePages = $posts->hasMorePages();

        return view('livewire.studio-animes-table', [
            'posts' => $posts,
            'years' => \App\Models\Year::orderBy('name', 'desc')->get(['id', 'name']),
            'seasons' => \App\Models\Season::all(['id', 'name']),
            'formats' => \App\Models\Format::all(['id', 'name']),
            'all_genres' => \App\Models\Genre::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
