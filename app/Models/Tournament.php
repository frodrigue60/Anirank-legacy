<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'size', 'type_filter', 'status', 'current_round',
        'winner_song_id', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function matchups()
    {
        return $this->hasMany(TournamentMatchup::class);
    }

    public function winner()
    {
        return $this->belongsTo(Song::class, 'winner_song_id');
    }
}
