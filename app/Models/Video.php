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
        'type',
        'disk',
        'song_variant_id',
    ];

    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($video) {
            if ($video->video_src && Storage::disk($video->disk)->exists($video->video_src)) {
                Storage::disk($video->disk)->delete($video->video_src);
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

    public function isEmbed()
    {
        return $this->type === 'embed';
    }

    public function isLocal()
    {
        return $this->type === 'file';
    }

    public function getEmbedUrlAttribute()
    {
        if (!$this->isEmbed()) return null;

        return $this->embed_code;
    }

    public function getLocalUrlAttribute()
    {
        if (!$this->isLocal()) return null;

        return Storage::disk($this->disk)->url($this->video_src);
    }
}
