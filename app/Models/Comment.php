<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Reaction;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'user_id', 'parent_id', 'song_id'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            if ($comment->song_id) {
                \App\Models\Activity::log(
                    $comment->user_id,
                    'comment',
                    $comment->song_id,
                    'song',
                    $comment->content
                );
            }
        });

        static::deleted(function ($comment) {
            // Nota: Podríamos borrar basándonos en el ID de comentario si lo guardamos en action_value como metadato,
            // pero por ahora borraremos por contenido y tipo si es necesario.
            // Para comentarios es mejor borrarlos manualmente o dejar que el cascade haga su trabajo si implementamos relaciones.
        });
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function reports()
    {
        return $this->hasMany(CommentReport::class);
    }

    // Relación para respuestas (hijos)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies', 'user'); // Eager loading para anidar respuestas
    }

    // Relación para el comentario padre (opcional)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->belongsToMany(User::class, 'comment_reactions', 'comment_id', 'user_id')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function likes()
    {
        return $this->reactions()->where('type', 1);
    }

    public function dislikes()
    {
        return $this->reactions()->where('type', -1);
    }

    public function liked()
    {
        if (Auth::check()) {
            return $this->reactions()
                ->where('user_id', Auth::id())
                ->where('type', 1)
                ->exists();
        }
        return false;
    }

    public function disliked()
    {
        if (Auth::check()) {
            return $this->reactions()
                ->where('user_id', Auth::id())
                ->where('type', -1)
                ->exists();
        }
        return false;
    }

    public function getLikesCountAttribute()
    {
        return $this->attributes['likes_count'] ?? 0;
    }

    public function getDislikesCountAttribute()
    {
        return $this->attributes['dislikes_count'] ?? 0;
    }

    // Método para actualizar los contadores (ahora actualiza las columnas directamente)
    public function updateReactionCounters()
    {
        $this->update([
            'likes_count' => $this->likes()->count(),
            'dislikes_count' => $this->dislikes()->count(),
        ]);
    }
}
