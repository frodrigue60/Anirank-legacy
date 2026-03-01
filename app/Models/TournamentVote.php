<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentVote extends Model
{
    protected $fillable = [
        'tournament_matchup_id', 'user_id', 'song_id', 'ip_address'
    ];

    public function matchup()
    {
        return $this->belongsTo(TournamentMatchup::class, 'tournament_matchup_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
