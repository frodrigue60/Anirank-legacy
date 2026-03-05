<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use App\Models\Year;
use App\Models\Season;
use App\Models\Artist;
use Illuminate\Support\Facades\Storage;

#[Lazy]
class ArtistThemesTable extends Component
{
    use WithPagination;

    public $artist;

    #[Url(except: '')]
    public $name = '';

    #[Url(except: '')]
    public $type = '';

    #[Url(except: '')]
    public $year_id = '';

    #[Url(except: '')]
    public $season_id = '';

    #[Url(except: 'recent')]
    public $sort = 'recent';

    public $perPage = 18;
    public $hasMorePages = false;

    public function placeholder()
    {
        return view('livewire.skeletons.artist-themes-skeleton');
    }

    public function updatingName()
    {
        $this->resetPage();
    }

    public function updatingType()
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

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->perPage += 18;
    }

    public function clearFilters()
    {
        $this->reset(['name', 'type', 'year_id', 'season_id', 'sort', 'perPage']);
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
    public function types()
    {
        return [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH'],
        ];
    }

    #[Computed]
    public function sortMethods()
    {
        return [
            ['name' => 'Recent', 'value' => 'recent'],
            ['name' => 'Title', 'value' => 'title'],
            ['name' => 'Score', 'value' => 'averageRating'],
            ['name' => 'Views', 'value' => 'view_count'],
            ['name' => 'Popular', 'value' => 'likeCount'],
        ];
    }

    public function render()
    {

        $query = Song::query()
            ->with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->withAvg('ratings', 'rating')
            ->whereHas('artists', function ($q) {
                $q->where('artists.id', $this->artist->id);
            })
            ->whereHas('anime', function ($q) {
                if (!Auth::check() || !Auth::user()->isStaff()) {
                    $q->where('status', true);
                }

                if ($this->name) {
                    $q->where('title', 'LIKE', '%' . $this->name . '%');
                }

                if ($this->season_id) {
                    $q->where('season_id', $this->season_id);
                }

                if ($this->year_id) {
                    $q->where('year_id', $this->year_id);
                }
            });

        if ($this->type && $this->type !== 'all') {
            $query->where('type', $this->type);
        }

        switch ($this->sort) {
            case 'title':
                $query->join('animes', 'songs.anime_id', '=', 'animes.id')
                    ->orderBy('animes.title');
                break;
            case 'averageRating':
                $query->orderByDesc('ratings_avg_rating');
                break;
            case 'view_count':
                $query->orderByDesc('view_count');
                break;
            case 'likeCount':
                $query->orderByDesc('likeCount');
                break;
            case 'recent':
            default:
                $query->orderByDesc('songs.created_at');
                break;
        }

        if ($this->sort === 'title') {
            $query->select('songs.*');
        }

        $songs = $query->paginate($this->perPage);
        $this->hasMorePages = $songs->hasMorePages();

        return view('livewire.artist-themes-table', [
            'songs' => $songs,
        ]);
    }
}
