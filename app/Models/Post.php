<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, \App\Traits\HasImages;
    protected $appends = ['thumbnail_url', 'banner_url'];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'anilist_id',
        'status',
        'year_id',
        'season_id',
        'format_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            foreach ($post->images as $image) {
                if (Storage::disk($image->disk)->exists($image->path)) {
                    Storage::disk($image->disk)->delete($image->path);
                }
                $image->delete();
            }
        });
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
        $this->status = !$this->status;
        return $this->save();
    }
}
