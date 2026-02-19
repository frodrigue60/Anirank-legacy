<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producer) {
            if (empty($producer->slug)) {
                $producer->slug = \Illuminate\Support\Str::slug($producer->name);
            }
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
