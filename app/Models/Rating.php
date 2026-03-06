<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'song_ratings';

    protected $fillable = ['rating', 'user_id', 'song_id'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($rating) {
            \App\Models\Activity::updateOrCreate(
                [
                    'user_id' => $rating->user_id,
                    'action_type' => 'rating',
                    'target_id' => $rating->song_id,
                    'target_type' => 'song',
                ],
                [
                    'action_value' => $rating->rating,
                    'created_at' => $rating->created_at,
                ]
            );
        });

        static::deleted(function ($rating) {
            \App\Models\Activity::where('user_id', $rating->user_id)
                ->where('action_type', 'rating')
                ->where('target_id', $rating->song_id)
                ->where('target_type', 'song')
                ->delete();
        });
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
