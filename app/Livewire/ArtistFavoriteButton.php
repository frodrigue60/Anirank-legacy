<?php

namespace App\Livewire;

use App\Models\Artist;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ArtistFavoriteButton extends Component
{
    public Artist $artist;
    public $isFavorite = false;
    public $favoriteCount = 0;

    public function mount(Artist $artist)
    {
        $this->artist = $artist;
        $this->checkIfFavorite();
        $this->refreshCount();
    }

    public function checkIfFavorite()
    {
        if (Auth::check()) {
            $this->isFavorite = $this->artist->favoritedBy()->where('user_id', Auth::id())->exists();
        } else {
            $this->isFavorite = false;
        }
    }

    public function refreshCount()
    {
        $this->favoriteCount = $this->artist->favoritedBy()->count();
    }

    public function toggle()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        $this->artist->toggleFavorite();
        $this->isFavorite = !$this->isFavorite;
        $this->refreshCount();

        // Dispatch events if needed (e.g., to notify activity feed or other components)
        $this->dispatch('artist-favorite-toggled', artistId: $this->artist->id, isFavorite: $this->isFavorite);
    }

    public function render()
    {
        return view('livewire.artist-favorite-button');
    }
}
