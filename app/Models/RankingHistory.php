<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_id',
        'rank',
        'seasonal_rank',
        'score',
        'date',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
