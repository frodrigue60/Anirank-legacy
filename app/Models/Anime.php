<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Anime extends Model
{
    use HasFactory, HasUuids, \App\Traits\Auditable, \App\Traits\PublishedScope;

    protected $appends = ['cover_url', 'banner_url'];

    protected $fillable = [
        'uuid',
        'anime_themes_id',
        'title',
        'slug',
        'description',
        'anilist_id',
        'banner',
        'status',
        'enabled_songs',
        'disabled_songs',
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

        static::deleting(function ($anime) {
            $disk = env('FILESYSTEM_DISK', 'public');
            if ($anime->cover && Storage::disk($disk)->exists($anime->cover)) {
                Storage::disk($disk)->delete($anime->cover);
            }
            if ($anime->banner && Storage::disk($disk)->exists($anime->banner)) {
                Storage::disk($disk)->delete($anime->banner);
            }
        });
    }

    public function getCoverUrlAttribute()
    {
        if (!$this->cover) return null;
        if (filter_var($this->cover, FILTER_VALIDATE_URL)) return $this->cover;
        return Storage::url($this->cover);
    }

    public function getBannerUrlAttribute()
    {
        if (!$this->banner) return null;
        if (filter_var($this->banner, FILTER_VALIDATE_URL)) return $this->banner;
        return Storage::url($this->banner);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }


    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function openings()
    {
        return $this->hasMany(Song::class)->where('type', Song::TYPE_OPENING);
    }

    public function endings()
    {
        return $this->hasMany(Song::class)->where('type', Song::TYPE_ENDING);
    }

    public function studios()
    {
        return $this->belongsToMany(Studio::class)->withTimestamps();
    }

    public function producers()
    {
        return $this->belongsToMany(Producer::class)->withTimestamps();
    }

    public function format()
    {
        return $this->belongsTo(Format::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    public function externalLinks()
    {
        return $this->belongsToMany(ExternalLink::class)->withTimestamps();
    }

    public function toggleStatus()
    {
        $this->status = ! $this->status;

        return $this->save();
    }

    /**
     * Update or create a specific type of image (cover or banner).
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
