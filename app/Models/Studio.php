<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    use HasFactory, \App\Traits\Auditable;
    protected $appends = ['logo_url'];

    protected $fillable = [
        'name',
        'slug',
        'logo'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($studio) {
            if (empty($studio->slug)) {
                $studio->slug = \Illuminate\Support\Str::slug($studio->name);
            }
        });

        static::deleting(function ($studio) {
            if ($studio->logo && !filter_var($studio->logo, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($studio->logo);
            }
        });
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) return null;
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) return $this->logo;
        return \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->logo);
    }

    // Keep avatar_url for backward compatibility if needed
    public function getAvatarUrlAttribute()
    {
        return $this->logo_url;
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class);
    }
}
