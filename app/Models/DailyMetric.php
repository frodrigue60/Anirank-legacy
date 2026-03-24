<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_id',
        'date',
        'views_count',
        'new_users_count',
        'new_ratings_count',
        'new_songs_count'
    ];

    /**
     * Get the song associated with the metric.
     */
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
