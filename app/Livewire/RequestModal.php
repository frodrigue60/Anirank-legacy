<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class RequestModal extends Component
{
    public $showModal = false;
    public $title;
    public $category;
    public $content;

    protected $rules = [
        'category' => 'required',
        'content'  => 'required|string|max:500',
    ];

    #[On('openRequestModal')]
    public function openRequestModal()
    {
        $this->reset(['category', 'content']);
        $this->showModal = true;
    }

    public function submitRequest()
    {
        if (!Auth::check()) {
            $this->dispatch('toast', message: 'Please login to send a request', type: 'warning');
            return;
        }

        $this->validate();

        $categories = [
            '1' => 'Add Anime/Song',
            '2' => 'Report Bug/Issue',
            '3' => 'Suggestion/Feedback',
        ];

        $requestTitle = $categories[$this->category] ?? 'Unknown Category';

        try {
            UserRequest::create([
                'title'   => $requestTitle,
                'content' => $this->content,
                'user_id' => Auth::id(),
                'status'  => 'pending',
            ]);

            $this->showModal = false;
            $this->reset(['category', 'content']);

            $this->dispatch('toast', message: 'Request Sent Successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to send request. Please try again.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.request-modal');
    }
}
