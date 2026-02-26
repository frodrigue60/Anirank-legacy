<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Song;
use App\Models\Year;
use App\Models\Season;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\HasRankingScore;
use Illuminate\Support\Facades\Auth;

class UserFavoritesTable extends Component
{
    use WithPagination;
    use HasRankingScore;

    public $userId;
    public $user;

    #[Url(except: '')]
    public $name = '';

    #[Url(except: '')]
    public $type = '';

    #[Url(except: '')]
    public $year_id = '';

    #[Url(except: '')]
    public $season_id = '';

    #[Url(except: '')]
    public $sort = '';

    public $perPage = 18;
    public $hasMorePages = true;
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::findOrFail($userId);
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
        if ($this->readyToLoad) {
            $this->perPage += 12;
        }
    }

    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.user-favorites-table', [
                'songs' => collect(),
                'years' => collect(),
                'seasons' => collect(),
                'sortMethods' => [],
                'types' => []
            ]);
        }

        $query = Song::query()
            ->with(['anime:id,title,slug', 'artists:id,name,slug'])
            ->withAvg('ratings', 'rating')
            ->favoritedBy($this->userId)
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->whereHas('anime', function ($query) {
                $query->where('status', true)
                    ->when($this->name, function ($query) {
                        $query->where('title', 'LIKE', '%' . $this->name . '%');
                    })
                    ->when($this->season_id, function ($query) {
                        $query->where('season_id', $this->season_id);
                    })
                    ->when($this->year_id, function ($query) {
                        $query->where('year_id', $this->year_id);
                    });
            });

        switch ($this->sort) {
            case 'title':
                $query->join('animes', 'songs.anime_id', '=', 'animes.id')
                    ->orderBy('animes.title', 'asc')
                    ->select('songs.*');
                break;
            case 'averageRating':
                $query->orderBy('ratings_avg_rating', 'desc');
                break;
            case 'view_count':
                $query->orderBy('views', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('songs.created_at', 'desc');
                break;
        }

        $songs = $query->paginate($this->perPage);
        $this->hasMorePages = $songs->hasMorePages();

        $this->setScoreSongs($songs, Auth::user());

        return view('livewire.user-favorites-table', [
            'songs' => $songs,
            'years' => Year::orderBy('name', 'desc')->get(['id', 'name']),
            'seasons' => Season::all(['id', 'name']),
            'sortMethods' => [
                ['name' => 'Sort by', 'value' => ''],
                ['name' => 'Recent', 'value' => 'recent'],
                ['name' => 'Title', 'value' => 'title'],
                ['name' => 'Score', 'value' => 'averageRating'],
                ['name' => 'Views', 'value' => 'view_count'],
            ],
            'types' => [
                ['name' => 'Opening', 'value' => 'OP'],
                ['name' => 'Ending', 'value' => 'ED'],
                ['name' => 'Insert', 'value' => 'INS'],
                ['name' => 'Other', 'value' => 'OTH']
            ]
        ]);
    }
}
