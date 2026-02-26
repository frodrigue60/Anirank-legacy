<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id', 'is_public'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class)
            ->withPivot('position')
            ->orderBy('position');
    }

    public function addAnime(Song $song)
    {
        if (! $this->songs()->where('songs.id', $song->id)->exists()) {
            $this->songs()->attach($song->id);
        }
    }

    public function removeAnime(Song $song)
    {
        $this->songs()->detach($song->id);
    }
}
