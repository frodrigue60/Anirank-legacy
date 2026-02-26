<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'icon',
        'type'
    ];

    public function anime()
    {
        return $this->belongsToMany(Anime::class);
    }
}
