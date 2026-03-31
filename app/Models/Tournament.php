<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use \App\Traits\HasUuid;
    protected $fillable = [
        'name', 'slug', 'description', 'size', 'type_filter', 'status', 'current_round',
        'winner_song_id', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($tournament) {
            foreach ($tournament->matchups as $matchup) {
                $matchup->delete();
            }
        });
    }

    public function matchups()
    {
        return $this->hasMany(TournamentMatchup::class);
    }

    public function winner()
    {
        return $this->belongsTo(Song::class, 'winner_song_id');
    }
}
