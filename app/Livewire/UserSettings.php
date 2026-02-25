<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserSettings extends Component
{
    use WithFileUploads;

    public $user;
    public $score_formats;

    #[Validate('nullable|image|mimes:jpeg,png,jpg,webp|max:512')]
    public $image;

    #[Validate('nullable|image|mimes:jpeg,png,jpg,webp|max:512')]
    public $banner;

    #[Validate('required|in:POINT_100,POINT_10_DECIMAL,POINT_10,POINT_5')]
    public $score_format;

    public function mount()
    {
        $this->user = Auth::user();
        $this->score_format = $this->user->score_format;
        $this->score_formats = [
            ['name' => '100 Point (100/100)', 'value' => 'POINT_100'],
            ['name' => '10.0 Point Decimal (10.0/10)', 'value' => 'POINT_10_DECIMAL'],
            ['name' => '10 Point (10/10)', 'value' => 'POINT_10'],
            ['name' => '5 Star (5.0/5)', 'value' => 'POINT_5'],
        ];
    }

    public function saveAvatar()
    {
        $this->validateOnly('image');

        try {
            $extension = $this->image->extension();
            $file_name = $this->user->slug . '-' . time() . '.' . $extension;
            $path = 'profile/' . $file_name;

            $storedPath = $this->image->storeAs('profile', $file_name);

            if ($storedPath) {
                $this->user->updateOrCreateImage($storedPath, 'avatar');
                $this->user->load('images');

                $this->dispatch('avatarUpdated', url: $this->user->avatar_url);
                session()->flash('avatar_success', 'Avatar updated successfully!');
                $this->reset('image');
            }
        } catch (\Exception $e) {
            session()->flash('avatar_error', 'Error updating avatar: ' . $e->getMessage());
        }
    }

    public function saveBanner()
    {
        $this->validateOnly('banner');

        try {
            $extension = $this->banner->extension();
            $file_name = $this->user->slug . '-' . time() . '.' . $extension;
            $path = 'banner/' . $file_name;

            $storedPath = $this->banner->storeAs('banner', $file_name);

            if ($storedPath) {
                $this->user->updateOrCreateImage($storedPath, 'banner');
                $this->user->load('images');

                $this->dispatch('bannerUpdated', url: $this->user->banner_url);
                session()->flash('banner_success', 'Banner updated successfully!');
                $this->reset('banner');
            }
        } catch (\Exception $e) {
            session()->flash('banner_error', 'Error updating banner: ' . $e->getMessage());
        }
    }

    public function saveScoreFormat()
    {
        $this->validateOnly('score_format');

        try {
            $this->user->update(['score_format' => $this->score_format]);
            session()->flash('settings_success', 'Scoring system preference updated!');
        } catch (\Exception $e) {
            session()->flash('settings_error', 'Error updating preferences: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-settings');
    }
}
