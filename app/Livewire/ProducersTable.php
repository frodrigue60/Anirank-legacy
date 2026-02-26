<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\Producer;
use Illuminate\Support\Facades\Auth;

#[Lazy]
class ProducersTable extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'name_asc')]
    public $sort = 'name_asc';

    public $perPage = 18;
    public $hasMorePages = false;

    public function placeholder()
    {
        return view('livewire.skeletons.producers-table-skeleton');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->perPage += 12;
        }
    }

    public function render()
    {

        $producersQuery = Producer::query()
            ->withCount(['animes' => function ($query) {
                if (!Auth::check() || !Auth::user()->isStaff()) {
                    $query->where('status', true);
                }
            }])
            ->whereHas('animes', function ($query) {
                if (!Auth::check() || !Auth::user()->isStaff()) {
                    $query->where('status', true);
                }
            })
            ->with(['animes' => function ($query) {
                if (!Auth::check() || !Auth::user()->isStaff()) {
                    $query->where('status', true);
                }
                $query->select(['animes.id']);
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->sort === 'name_asc', function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->when($this->sort === 'name_desc', function ($query) {
                $query->orderBy('name', 'desc');
            })
            ->when($this->sort === 'most_series', function ($query) {
                $query->orderBy('animes_count', 'desc');
            })
            ->when($this->sort === 'least_series', function ($query) {
                $query->orderBy('animes_count', 'asc');
            });

        $results = $producersQuery->take($this->perPage + 1)->get();
        $this->hasMorePages = $results->count() > $this->perPage;
        $producers = $results->take($this->perPage);

        return view('livewire.producers-table', [
            'producers' => $producers,
        ]);
    }
}
