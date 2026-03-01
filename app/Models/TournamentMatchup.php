<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentMatchup extends Model
{
    protected $fillable = [
        'tournament_id', 'round', 'position', 'song1_id', 'song2_id',
        'song1_votes', 'song2_votes', 'winner_song_id', 'ends_at', 'is_active'
    ];

    protected $casts = [
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($matchup) {
            foreach ($matchup->votes as $vote) {
                $vote->delete();
            }
        });
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function song1()
    {
        return $this->belongsTo(Song::class, 'song1_id');
    }

    public function song2()
    {
        return $this->belongsTo(Song::class, 'song2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Song::class, 'winner_song_id');
    }

    public function votes()
    {
        return $this->hasMany(TournamentVote::class);
    }
}
