<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\Artist;
use Illuminate\Support\Facades\Auth;

#[Lazy]
class ArtistsTable extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $name = '';

    #[Url(except: 'A-Z')]
    public $sortBy = 'A-Z';

    #[Url(except: 'Most Themes')]
    public $sortByThemes = 'Most Themes';

    public $perPage = 24;
    public $hasMorePages = false;

    public function placeholder()
    {
        return view('livewire.skeletons.artists-table-skeleton');
    }

    public function updatingName()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingSortByThemes()
    {
        $this->resetPage();
    }

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->perPage += 24;
        }
    }

    public function clearFilters()
    {
        $this->reset(['name', 'sortBy', 'sortByThemes', 'perPage']);
    }

    public function render()
    {

        $query = Artist::query()
            ->select(['id', 'name', 'slug'])
            ->withCount('songs');

        if ($this->name) {
            $query->where('name', 'LIKE', '%' . $this->name . '%');
        }

        if ($this->sortBy === 'A-Z') {
            $query->orderBy('name', 'asc');
        } elseif ($this->sortBy === 'Z-A') {
            $query->orderBy('name', 'desc');
        } elseif ($this->sortBy === 'most_themes') {
            $query->orderBy('songs_count', 'desc');
        } elseif ($this->sortBy === 'least_themes') {
            $query->orderBy('songs_count', 'asc');
        }

        $results = $query->take($this->perPage + 1)->get();
        $this->hasMorePages = $results->count() > $this->perPage;
        $artists = $results->take($this->perPage);

        return view('livewire.artists-table', [
            'artists' => $artists,
        ]);
    }
}
