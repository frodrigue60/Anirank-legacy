<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    protected static function booted()
    {
        static::saving(function ($genre) {
            if (empty($genre->slug) || $genre->isDirty('name')) {
                $slug = Str::slug($genre->name);
                $originalSlug = $slug;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $genre->id ?? 0)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }
                $genre->slug = $slug;
            }
        });
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class);
    }
}
