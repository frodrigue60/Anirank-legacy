<?php

namespace App\Livewire;

use Livewire\Component;

class AnnouncementSidebar extends Component
{
    /**
     * Fetch active announcements.
     * Use Computed to cache the result during the request.
     */
    #[\Livewire\Attributes\Computed]
    public function announcements()
    {
        return \App\Models\Announcement::active()->get();
    }

    public function render()
    {
        return view('livewire.announcement-sidebar', [
            'announcements' => $this->announcements
        ]);
    }
}
