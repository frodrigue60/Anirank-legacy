<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SongVariant;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{

    protected $fillable = [
        'embed_code',
        'video_src',
        'song_variant_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    use \Illuminate\Database\Eloquent\Factories\HasFactory, \App\Traits\Auditable, \App\Traits\PublishedScope;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole('creator')) {
                $model->status = false;
            }
        });

        static::deleting(function ($video) {
            if ($video->video_src && Storage::disk()->exists($video->video_src)) {
                Storage::disk()->delete($video->video_src);
            }
        });
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function songVariant()
    {
        return $this->belongsTo(SongVariant::class);
    }

    /**
     * Resolves the best available video source based on existence.
     * 1. S3 File (video_src)
     * 2. Embed Code
     * @return array|null
     */
    public function getSourceDataAttribute()
    {
        if ($this->video_src && Storage::disk()->exists($this->video_src)) {
            return [
                'url' => Storage::url($this->video_src),
                'type' => 'file'
            ];
        }

        if ($this->embed_code) {
            return [
                'url' => $this->embed_code,
                'type' => 'embed'
            ];
        }

        return null;
    }

    public function isEmbed()
    {
        return $this->source_data['type'] === 'embed' ?? false;
    }

    public function isLocal()
    {
        return $this->source_data['type'] === 'file' ?? false;
    }

    public function getEmbedUrlAttribute()
    {
        return $this->isEmbed() ? $this->embed_code : null;
    }

    public function getLocalUrlAttribute()
    {
        return $this->isLocal() ? Storage::url($this->video_src) : null;
    }
}
