<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Format extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'name',
        'slug'
    ];

    protected static function booted()
    {
        static::saving(function ($format) {
            if (empty($format->slug) || $format->isDirty('name')) {
                $slug = Str::slug($format->name);
                $originalSlug = $slug;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $format->id ?? 0)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }
                $format->slug = $slug;
            }
        });
    }

    public function animes()
    {
        return $this->hasMany(Anime::class);
    }
}
