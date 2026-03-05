<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\Song;
use App\Models\Year;
use App\Models\Season;

#[Lazy]
class SongsTable extends Component
{
    use WithPagination;

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
    public $hasMorePages = true;

    public function placeholder()
    {
        return view('livewire.skeletons.songs-table-skeleton');
    }

    public function updatingName()
    {
        $this->resetPage();
        $this->perPage = 18;
    }

    public function updatingType()
    {
        $this->resetPage();
        $this->perPage = 18;
    }

    public function updatingYearId()
    {
        $this->resetPage();
        $this->perPage = 18;
    }

    public function updatingSeasonId()
    {
        $this->resetPage();
        $this->perPage = 18;
    }

    public function updatingSort()
    {
        $this->resetPage();
        $this->perPage = 18;
    }

    public function loadMore()
    {
        $this->perPage += 18;
    }

    #[Computed]
    public function years()
    {
        return Year::select('id', 'name')->orderBy('name', 'desc')->get();
    }

    #[Computed]
    public function seasons()
    {
        return Season::select('id', 'name')->get();
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
            ->withCount('likes')
            ->whereHas('anime', function ($q) {
                $q->where('status', true);
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
                    ->orderBy('animes.title')
                    ->select('songs.*');
                break;
            case 'averageRating':
                $query->orderByDesc('ratings_avg_rating');
                break;
            case 'view_count':
                $query->orderByDesc('views');
                break;
            case 'likeCount':
                $query->orderByDesc('likes_count');
                break;
            case 'recent':
            default:
                $query->orderByDesc('songs.created_at');
                break;
        }

        $totalSelection = $query->count();
        $songs = $query->take($this->perPage)->get();

        $this->hasMorePages = ($songs->count() < $totalSelection);

        return view('livewire.songs-table', [
            'songs' => $songs,
        ]);
    }
}
