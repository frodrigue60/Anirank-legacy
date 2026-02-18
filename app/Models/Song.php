<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\SongVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rateable;
use Illuminate\Support\Facades\Session;
use App\Models\DailyMetric;

class Song extends Model
{
    use HasFactory;
    use Rateable;

    protected $fillable = [
        'song_romaji',
        'song_jp',
        'song_en',
        'theme_num',
        'type',
        'slug',
        'post_id',
        'season_id',
        'year_id',
        'views'
    ];

    public const TYPE_OPENING = 'OP';
    public const TYPE_ENDING = 'ED';

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($song) {
            foreach ($song->songVariants as $variant) {
                $variant->delete();
            }
        });
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->whereNull('song_variant_id');
    }

    public function songVariants()
    {
        return $this->hasMany(SongVariant::class);
    }

    public function firstSongVariant()
    {
        return $this->hasOne(SongVariant::class)->orderBy('version_number');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function incrementViews()
    {
        $key = 'song_' . $this->id;

        if (!Session::has($key)) {
            DB::beginTransaction();
            try {
                // Static global count
                DB::table('songs')
                    ->where('id', $this->id)
                    ->increment('views');

                // Daily time-series count
                DailyMetric::updateOrCreate(
                    ['song_id' => $this->id, 'date' => now()->toDateString()],
                    ['views_count' => DB::raw('views_count + 1')]
                );

                DB::commit();
                Session::put($key, true);
            } catch (\Exception $e) {
                DB::rollBack();
                // Optionally log error
            }
        }
    }

    public function getNameAttribute()
    {
        return $this->song_romaji ?? $this->song_en ?? $this->song_jp ?? 'n/a';
    }

    public function getUrlFirstVariantAttribute()
    {
        // Cargar relaciones necesarias si no están ya cargadas
        if (!$this->relationLoaded('post') || !$this->post->relationLoaded('songs')) {
            $this->load(['post', 'songVariants']);
        }

        $smallestVariant = $this->post->songs->flatMap(function ($song) {
            return $song->songVariants;
        })->sortBy('version_number')->first();

        return route('songs.show.nested', [
            'post' => $this->post->slug,
            'song' => $this->slug,
        ]);
    }

    public function getUrlAttribute()
    {
        return $this->url_first_variant;
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function likes()
    {
        return $this->reactions()->where('type', 1);
    }

    public function dislikes()
    {
        return $this->reactions()->where('type', -1);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->dislikes()->count();
    }

    // Método para verificar si el usuario actual ha dado like
    public function liked()
    {
        if (Auth::check()) { // Verifica si el usuario está autenticado
            return $this->reactions()
                ->where('user_id', Auth::id())
                ->where('type', 1)
                ->exists();
        }
        return false;
    }

    // Método para verificar si el usuario actual ha dado dislike
    public function disliked()
    {
        if (Auth::check()) { // Verifica si el usuario está autenticado
            return $this->reactions()
                ->where('user_id', Auth::id())
                ->where('type', -1)
                ->exists();
        }
        return false;
    }

    public function getViewsStringAttribute()
    {
        if ($this->views >= 1000000) {
            $views = number_format(intval($this->views / 1000000), 0) . 'M';
        } elseif ($this->views >= 1000) {
            $views = number_format(intval($this->views / 1000), 0) . 'K';
        } else {
            $views = $this->views;
        }

        return $views;
    }

    // Método para obtener los posts que un usuario ha marcado como favorito
    public function scopeFavoritedBy($query, $userId)
    {
        return $query->whereHas('favorites', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // Relación con el contador de reacciones (nombre corregido)
    public function reactionsCounter()
    {
        return $this->morphOne(ReactionCounter::class, 'reactable');
    }

    // Método para actualizar los contadores
    public function updateReactionCounters()
    {
        $likesCount = $this->reactions()->where('type', 1)->count();
        $dislikesCount = $this->reactions()->where('type', -1)->count();

        $this->reactionsCounter()->updateOrCreate(
            ['reactable_id' => $this->id, 'reactable_type' => self::class],
            ['likes_count' => $likesCount, 'dislikes_count' => $dislikesCount]
        );
    }

    // Relación polimórfica para favoritos, retorna array con la relacion
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    // retorna el la cantidad de veces que ha sido marcado como favorito
    public function getFavoritesCountAttribute()
    {
        return $this->favorites()->count();
    }

    // Método para verificar si el usuario actual ha marcado este post como favorito
    public function isFavorited()
    {
        if (Auth::check()) {
            return $this->favorites()->where('user_id', Auth::id())->exists();
        }
        return false;
    }

    public function toggleFavorite()
    {
        if (!Auth::check()) {
            return false;
        }

        $userId = Auth::id();
        $favorite = $this->favorites()->where('user_id', $userId)->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        } else {
            $this->favorites()->create(['user_id' => $userId]);
            return true;
        }
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class);
    }

    public function isInPlaylist($playlistId)
    {
        return $this->playlists()->where('playlist_id', $playlistId)->exists();
    }

    /* public function getUserRatingAttribute($song_id)
    {
        $user = Auth::user();
        $userRating = DB::table('ratings')
            ->where('rateable_type', $this::class)
            ->where('rateable_id', $song_id)
            ->where('user_id', $user->id)
            ->first(['rating']);

        return $userRating;
    } */

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function rankingHistory()
    {
        return $this->hasMany(RankingHistory::class);
    }

    public function getPreviousRank()
    {
        return $this->rankingHistory()
            ->where('date', '<', now()->toDateString())
            ->orderBy('date', 'desc')
            ->value('rank');
    }

    public function getPreviousSeasonalRank()
    {
        return $this->rankingHistory()
            ->where('date', '<', now()->toDateString())
            ->orderBy('date', 'desc')
            ->value('seasonal_rank');
    }

    // -------------------------------------------------------------------------
    // Score Formatting Helpers
    // -------------------------------------------------------------------------

    /**
     * Converts a raw score (0–100) to the given display format.
     */
    private function convertScore(float|null $raw, string $format): int|float|null
    {
        if ($raw === null) return null;

        return match ($format) {
            'POINT_100'        => (int) round($raw),
            'POINT_10'         => (int) round($raw / 10),
            'POINT_10_DECIMAL' => round($raw / 10, 1),
            'POINT_5'          => round($raw / 20, 1),
            default            => (int) round($raw),
        };
    }

    /**
     * Returns the global average score formatted for the given score format.
     * Respects withAvg('ratings', 'rating') eager loading via the Rateable accessor.
     *
     * Usage: $song->formattedAvgScore($user->score_format)
     */
    public function formattedAvgScore(string $format = 'POINT_100'): int|float|null
    {
        return $this->convertScore($this->averageRating, $format);
    }

    /**
     * Returns the personal score of the given user (defaults to auth user)
     * formatted for the given score format.
     *
     * Usage: $song->formattedUserScore($user->score_format)
     *        $song->formattedUserScore($user->score_format, $user->id)
     */
    public function formattedUserScore(string $format = 'POINT_100', ?int $userId = null): int|float|null
    {
        $userId ??= auth()->id();
        if (!$userId) return null;

        // Uses the ratings() relationship from the Rateable trait (polymorphic).
        $raw = $this->ratings()->where('user_id', $userId)->value('rating');

        return $this->convertScore($raw, $format);
    }
}
