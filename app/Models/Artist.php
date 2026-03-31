<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, \App\Traits\Auditable, \App\Traits\PublishedScope, \App\Traits\HasUuid;
    protected $appends = ['avatar_url'];

    protected $fillable = [
        'name',
        'name_jp',
        'slug',
        'avatar',
        'status',
        'favorites_count',
        'enabled_songs',
        'disabled_songs',
        'animethemes_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole('creator')) {
                $model->status = false;
            }
        });

        static::creating(function ($artist) {
            if (empty($artist->slug)) {
                $artist->slug = \Illuminate\Support\Str::slug($artist->name);
            }
        });

        static::deleting(function ($artist) {
            // Desvincula todas las canciones asociadas
            $artist->songs()->detach();

            $disk = env('FILESYSTEM_DISK', 'public');
            if ($artist->avatar && \Illuminate\Support\Facades\Storage::disk($disk)->exists($artist->avatar)) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($artist->avatar);
            }
        });
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) return $this->avatar;
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }

        return null;
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'artist_user')->withTimestamps();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->attributes['favorites_count'] ?? 0;
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }

    public function toggleFavorite($userId = null)
    {
        $userId ??= \Illuminate\Support\Facades\Auth::id() ?? auth('sanctum')->id();
        
        if (!$userId) return false;

        $results = $this->favoritedBy()->toggle($userId);
        $isAttached = count($results['attached']) > 0;

        if ($isAttached) {
            \App\Models\Activity::log($userId, 'favorite_artist', $this->id, 'artist');
        } else {
            \App\Models\Activity::where('user_id', $userId)
                ->where('action_type', 'favorite_artist')
                ->where('target_id', $this->id)
                ->delete();
        }

        return $isAttached;
    }

    /**
     * Update or create a specific type of image (avatar).
     */
    public function updateOrCreateImage(string $path, string $type)
    {
        $disk = config('filesystems.default');
        $oldPath = $this->{$type};

        if ($oldPath && \Illuminate\Support\Facades\Storage::disk($disk)->exists($oldPath)) {
            \Illuminate\Support\Facades\Storage::disk($disk)->delete($oldPath);
        }

        $this->update([
            $type => $path
        ]);

        return $this;
    }
}
