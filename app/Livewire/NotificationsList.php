<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class NotificationsList extends Component
{
    use WithPagination;

    public string $activeCategory = 'all';

    protected $queryString = [
        'activeCategory' => ['except' => 'all', 'as' => 'category'],
    ];

    public function setCategory(string $category)
    {
        $this->activeCategory = $category;
        $this->resetPage();
    }

    public function getNotificationsProperty()
    {
        $query = Auth::user()->notifications()->latest();

        if ($this->activeCategory !== 'all') {
            $query->where('type', $this->activeCategory);
        }

        return $query->paginate(20);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        $this->dispatch('notification-read');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        
        $this->dispatch('notification-read');
    }

    public function delete($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->delete();
        
        $this->dispatch('notification-read');
    }

    public function render()
    {
        return view('livewire.notifications-list', [
            'notifications' => $this->notifications
        ]);
    }
}
