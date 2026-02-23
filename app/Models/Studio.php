<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    use HasFactory, \App\Traits\HasImages;
    protected $appends = ['avatar_url'];

    protected $fillable = [
        'name',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($studio) {
            if (empty($studio->slug)) {
                $studio->slug = \Illuminate\Support\Str::slug($studio->name);
            }
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
