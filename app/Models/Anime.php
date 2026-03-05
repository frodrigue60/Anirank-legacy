<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Anime extends Model
{
    use HasFactory;

    protected $appends = ['cover_url', 'banner_url'];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'anilist_id',
        'status',
        'year_id',
        'season_id',
        'format_id',
        'cover',
        'banner',
    ];

    protected static function boot()
    {
        parent::boot();

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
        return Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->cover);
    }

    public function getBannerUrlAttribute()
    {
        if (!$this->banner) return null;
        if (filter_var($this->banner, FILTER_VALIDATE_URL)) return $this->banner;
        return Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->banner);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
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
        return $this->belongsToMany(Studio::class);
    }

    public function producers()
    {
        return $this->belongsToMany(Producer::class);
    }

    public function format()
    {
        return $this->belongsTo(Format::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function externalLinks()
    {
        return $this->belongsToMany(ExternalLink::class);
    }

    public function toggleStatus()
    {
        $this->status = ! $this->status;

        return $this->save();
    }
}
