<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;
    protected $appends = ['avatar_url'];

    protected $fillable = [
        'name',
        'name_jp',
        'slug',
        'avatar',
    ];

    protected static function boot()
    {
        parent::boot();

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
            return \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->avatar);
        }

        return null;
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'artist_user')->withTimestamps();
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
}
