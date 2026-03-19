<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

#[Lazy]
class RankingUsers extends Component
{
    public $sort = 'xp'; // 'xp', 'ratings_count', 'comments_count'
    public $perPage = 20;
    public $page = 1;
    public $hasMorePages = true;

    public function placeholder()
    {
        return view('livewire.skeletons.ranking-users-skeleton');
    }

    public function mount()
    {
        $this->sort = 'xp';
    }

    public function setSort($criteria)
    {
        if (in_array($criteria, ['xp', 'ratings_count', 'comments_count'])) {
            $this->sort = $criteria;
            $this->page = 1;
            $this->hasMorePages = true;
        }
    }

    public function loadMore()
    {
        if (!$this->hasMorePages) return;
        $this->page++;
    }

    #[Computed]
    public function users()
    {
        $perPageTotal = $this->perPage * $this->page;

        $query = User::query()
            ->with(['roles'])
            ->withCount(['ratings', 'comments']);

        if ($this->sort === 'xp') {
            $query->orderByDesc('xp')->orderByDesc('id');
        } elseif ($this->sort === 'ratings_count') {
            $query->orderByDesc('ratings_count')->orderByDesc('xp');
        } elseif ($this->sort === 'comments_count') {
            $query->orderByDesc('comments_count')->orderByDesc('xp');
        }

        $users = $query->take($perPageTotal)->get();

        $this->hasMorePages = $users->count() >= $perPageTotal;

        // Assign Rank
        foreach ($users as $index => $user) {
            $user->rank = $index + 1;
        }

        return $users;
    }

    public function render()
    {
        return view('livewire.ranking-users', [
            'users' => $this->users,
        ]);
    }
}
