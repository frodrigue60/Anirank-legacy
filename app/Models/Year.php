<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Year extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function animes()
    {
        return $this->hasMany(Anime::class);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function songVariants()
    {
        return $this->hasMany(SongVariant::class);
    }

    public function setCurrent()
    {
        return DB::transaction(function () {
            static::query()->update(['current' => false]);
            $this->current = true;
            return $this->save();
        });
    }
}
