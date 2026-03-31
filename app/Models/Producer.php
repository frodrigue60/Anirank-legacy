<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    use HasFactory, \App\Traits\Auditable, \App\Traits\HasUuid;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'anime_count'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producer) {
            if (empty($producer->slug)) {
                $producer->slug = \Illuminate\Support\Str::slug($producer->name);
            }
        });

        static::deleting(function ($producer) {
            if ($producer->logo && !filter_var($producer->logo, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($producer->logo);
            }
        });
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) return null;
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) return $this->logo;
        return \Illuminate\Support\Facades\Storage::url($this->logo);
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class);
    }
}
