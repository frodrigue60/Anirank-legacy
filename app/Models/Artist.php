<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory, \App\Traits\HasImages;
    protected $appends = ['avatar_url'];

    protected $fillable = [
        'name',
        'name_jp',
        'slug',
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

            // Delete polymorphic images
            foreach ($artist->images as $image) {
                if (\Illuminate\Support\Facades\Storage::disk($image->disk)->exists($image->path)) {
                    \Illuminate\Support\Facades\Storage::disk($image->disk)->delete($image->path);
                }
                $image->delete();
            }
        });
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
}
