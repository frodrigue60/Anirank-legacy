<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

trait HasImages
{
    /**
     * Get all of the model's images.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the model's thumbnail.
     */
    public function getThumbnailUrlAttribute()
    {
        $image = $this->images->where('type', 'thumbnail')->first();
        return $image ? Storage::url($image->path) : asset('img/placeholders/default-thumbnail.webp');
    }

    /**
     * Get the model's banner.
     */
    public function getBannerUrlAttribute()
    {
        $image = $this->images->where('type', 'banner')->first();
        return $image ? Storage::url($image->path) : asset('img/placeholders/default-banner.webp');
    }

    /**
     * Get the model's avatar.
     */
    public function getAvatarUrlAttribute()
    {
        $image = $this->images->where('type', 'avatar')->first();
        if ($image) {
            return Storage::url($image->path);
        }

        $name = isset($this->name) ? urlencode($this->name) : 'User';
        // Random background, white text for premium contrast
        return "https://ui-avatars.com/api/?name={$name}&color=fff&background=random";
    }

    /**
     * Update or create a specific type of image.
     */
    public function updateOrCreateImage(string $path, string $type, string $disk = null)
    {
        $disk = $disk ?? config('filesystems.default');
        $oldImage = $this->images()->where('type', $type)->first();

        if ($oldImage) {
            if (Storage::disk($oldImage->disk)->exists($oldImage->path)) {
                Storage::disk($oldImage->disk)->delete($oldImage->path);
            }
            $oldImage->update([
                'path' => $path,
                'disk' => $disk
            ]);
            return $oldImage;
        }

        return $this->images()->create([
            'path' => $path,
            'type' => $type,
            'disk' => $disk
        ]);
    }
}
