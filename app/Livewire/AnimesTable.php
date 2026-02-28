<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\Anime;
use App\Models\Season;
use App\Models\Year;
use App\Models\Format;
use App\Models\Genre;

#[Lazy]
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

    #[Url(except: 'title_asc')]
    public $sort_by = 'title_asc';
    
    public $perPage = 15;
    public $hasMorePages = false;
    public function placeholder()
    {
        return view('livewire.skeletons.animes-table-skeleton');
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

    public function updatedSortBy()
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
        if ($this->hasMorePages) {
            $this->perPage += 15;
        }
    }

    #[Computed]
    public function years()
    {
        return Year::orderBy('name', 'desc')->get(['id', 'name']);
    }

    #[Computed]
    public function seasons()
    {
        return Season::all(['id', 'name']);
    }

    #[Computed]
    public function formats()
    {
        return Format::all(['id', 'name']);
    }

    #[Computed]
    public function all_genres()
    {
        return Genre::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function sort_bys()
    {
        return [
            'title_asc' => 'Title (A-Z)',
            'title_desc' => 'Title (Z-A)',
            'songs_count_desc' => 'Most Themes',
            'songs_count_asc' => 'Least Themes',
        ];
    }

    public function render()
    {
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

        $query->with(['format:id,name', 'season:id,name', 'year:id,name', 'studios:id,name,slug', 'genres:id,name'])
            ->withCount('songs');

        switch ($this->sort_by) {
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'songs_count_desc':
                $query->orderBy('songs_count', 'desc');
                break;
            case 'songs_count_asc':
                $query->orderBy('songs_count', 'asc');
                break;
            default:
                $query->orderBy('title', 'asc');
                break;
        }

        $results = $query->orderBy('id', 'asc')
            ->take($this->perPage + 1)
            ->get();

        $this->hasMorePages = $results->count() > $this->perPage;
        $animes = $results->take($this->perPage);

        return view('livewire.animes-table', [
            'animes' => $animes,
        ]);
    }
}
