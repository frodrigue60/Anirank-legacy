<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class FollowButton extends Component
{
    public User $user;
    public bool $isFollowing = false;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->checkIfFollowing();
    }

    public function checkIfFollowing()
    {
        if (Auth::check()) {
            $this->isFollowing = Auth::user()->isFollowing($this->user);
        } else {
            $this->isFollowing = false;
        }
    }

    public function toggle()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        Auth::user()->toggleFollow($this->user);
        $this->isFollowing = !$this->isFollowing;

        $this->dispatch('user-follow-toggled', followedId: $this->user->id, isFollowing: $this->isFollowing);
    }

    public function render()
    {
        return view('livewire.follow-button');
    }
}
