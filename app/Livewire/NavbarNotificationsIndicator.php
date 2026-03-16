<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class NavbarNotificationsIndicator extends Component
{
    public int $unreadCount = 0;

    public function mount()
    {
        $this->refreshCount();
    }

    #[On('user-follow-toggled')]
    #[On('notification-read')]
    public function refreshCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        } else {
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.navbar-notifications-indicator');
    }
}
