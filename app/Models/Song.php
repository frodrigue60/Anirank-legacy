<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Song extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, \App\Traits\Auditable, \App\Traits\PublishedScope;

    protected $appends = [
        'name',
        'likes_count',
        'dislikes_count',
        'liked',
        'disliked',
        'is_favorited',
        'average_rating',
    ];

    protected $fillable = [
        'song_romaji',
        'song_jp',
        'song_en',
        'theme_num',
        'type',
        'prev_seasonal_rank',
        'status',
        'favorites_count',
        'average_score',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public const TYPE_OPENING = 'OP';

    public const TYPE_ENDING = 'ED';

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole('creator')) {
                $model->status = false;
            }
        });

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

    public function anime()
    {
        return $this->belongsTo(Anime::class);
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

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function incrementViews()
    {
        $key = 'song_'.$this->id;

        if (! Session::has($key)) {
            DB::beginTransaction();
            try {
                // Static global count (Trigger handles DailyMetric update)
                DB::table('songs')
                    ->where('id', $this->id)
                    ->increment('views');

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
        if (! $this->relationLoaded('anime') || ! $this->anime->relationLoaded('songs')) {
            $this->load(['anime', 'songVariants']);
        }

        $smallestVariant = $this->anime->songs->flatMap(function ($song) {
            return $song->songVariants;
        })->sortBy('version_number')->first();

        return route('songs.show.nested', [
            'anime' => $this->anime->slug,
            'song' => $this->slug,
        ]);
    }

    public function getUrlAttribute()
    {
        return $this->url_first_variant;
    }

    public function reactions()
    {
        return $this->belongsToMany(User::class, 'song_reactions', 'song_id', 'user_id')
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

    public function getLikesCountAttribute()
    {
        return (int) ($this->attributes['likes_count'] ?? 0);
    }

    public function getDislikesCountAttribute()
    {
        return (int) ($this->attributes['dislikes_count'] ?? 0);
    }

    public function getAverageRatingAttribute()
    {
        return $this->attributes['average_score'] ?? 0;
    }

    // Método para verificar si el usuario actual ha dado like
    public function getLikedAttribute()
    {
        if (array_key_exists('liked', $this->attributes)) {
            return (bool) $this->attributes['liked'];
        }
        if (array_key_exists('reactions_exists', $this->attributes)) {
            return (bool) $this->attributes['reactions_exists'];
        }

        if (auth('sanctum')->check()) { // Verifica si el usuario está autenticado vía API
            return $this->reactions()
                ->where('user_id', auth('sanctum')->id())
                ->where('type', 1)
                ->exists();
        }

        return false;
    }

    // Método para verificar si el usuario actual ha dado dislike
    public function getDislikedAttribute()
    {
        if (array_key_exists('disliked', $this->attributes)) {
            return (bool) $this->attributes['disliked'];
        }

        if (auth('sanctum')->check()) { // Verifica si el usuario está autenticado vía API
            return $this->reactions()
                ->where('user_id', auth('sanctum')->id())
                ->where('type', -1)
                ->exists();
        }

        return false;
    }

    public function getViewsStringAttribute()
    {
        if ($this->views >= 1000000) {
            $views = number_format(intval($this->views / 1000000), 0).'M';
        } elseif ($this->views >= 1000) {
            $views = number_format(intval($this->views / 1000), 0).'K';
        } else {
            $views = $this->views;
        }

        return $views;
    }

    // Método para obtener los songs que un usuario ha marcado como favorito
    public function scopeFavoritedBy($query, $userId)
    {
        return $query->whereHas('favorites', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // Método para actualizar los contadores
    public function updateReactionCounters()
    {
        $this->update([
            'likes_count' => $this->likes()->count(),
            'dislikes_count' => $this->dislikes()->count(),
        ]);
    }

    // Relación para favoritos (Muchos a Muchos con User)
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'song_user')->withTimestamps();
    }

    // retorna la cantidad de veces que ha sido marcado como favorito
    public function getFavoritesCountAttribute()
    {
        return $this->attributes['favorites_count'] ?? 0;
    }

    public function scopeWithUserInteractions($query)
    {
        $guard = auth('sanctum')->check() ? 'sanctum' : null;
        if (! $guard) {
            return $query;
        }

        $userId = auth($guard)->id();

        return $query->withExists([
            'reactions as liked' => fn ($q) => $q->where('user_id', $userId)->where('type', 1),
            'reactions as disliked' => fn ($q) => $q->where('user_id', $userId)->where('type', -1),
            'favorites as is_favorited' => fn ($q) => $q->where('user_id', $userId),
        ]);
    }

    public function loadUserInteractions()
    {
        $guard = auth('sanctum')->check() ? 'sanctum' : null;
        if (! $guard) {
            return $this;
        }

        $userId = auth($guard)->id();

        return $this->loadExists([
            'reactions as liked' => fn ($q) => $q->where('user_id', $userId)->where('type', 1),
            'reactions as disliked' => fn ($q) => $q->where('user_id', $userId)->where('type', -1),
            'favorites as is_favorited' => fn ($q) => $q->where('user_id', $userId),
        ]);
    }

    // Método para verificar si el usuario actual ha marcado este anime como favorito
    public function getIsFavoritedAttribute()
    {
        if (array_key_exists('is_favorited', $this->attributes)) {
            return (bool) $this->attributes['is_favorited'];
        }
        if (array_key_exists('favorites_exists', $this->attributes)) {
            return (bool) $this->attributes['favorites_exists'];
        }

        $guard = auth('sanctum')->check() ? 'sanctum' : null;
        $userId = $guard ? auth($guard)->id() : null;

        return $userId ? $this->favorites()->where('user_id', $userId)->exists() : false;
    }

    public function toggleFavorite($userId = null)
    {
        $userId ??= Auth::id() ?? auth('sanctum')->id();
        
        if (!$userId) return false;

        $results = $this->favorites()->toggle($userId);
        $isAttached = count($results['attached']) > 0;

        if ($isAttached) {
            \App\Models\Activity::log($userId, 'favorite_song', $this->id, 'song');
        } else {
            \App\Models\Activity::where('user_id', $userId)
                ->where('action_type', 'favorite_song')
                ->where('target_id', $this->id)
                ->delete();
        }

        return $isAttached;
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
        return $this->hasMany(SongReport::class);
    }

    public function rankingHistory()
    {
        return $this->hasMany(RankingHistory::class);
    }

    public function previousRanking()
    {
        return $this->hasOne(RankingHistory::class)
            ->where('date', '<', now()->toDateString())
            ->orderBy('date', 'desc');
    }

    public function getPreviousRank()
    {
        return $this->prev_main_rank;
    }

    public function getPreviousSeasonalRank()
    {
        return $this->prev_seasonal_rank;
    }

    // -------------------------------------------------------------------------
    // Score Formatting Helpers
    // -------------------------------------------------------------------------

    /**
     * Converts a raw score (0–100) to the given display format.
     */
    private function convertScore(?float $raw, string $format): int|float|null
    {
        if ($raw === null) {
            return null;
        }

        return match ($format) {
            'POINT_100' => (int) round($raw),
            'POINT_10' => (int) round($raw / 10),
            'POINT_10_DECIMAL' => round($raw / 10, 1),
            'POINT_5' => round($raw / 20, 1),
            default => (int) round($raw),
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
        $userId ??= \Illuminate\Support\Facades\Auth::id();
        if (! $userId) {
            return null;
        }

        // Uses the ratings() relationship from the Rateable trait (polymorphic).
        if ($this->relationLoaded('ratings')) {
            $raw = $this->ratings->where('user_id', $userId)->first()?->rating;
        } else {
            $raw = $this->ratings()->where('user_id', $userId)->value('rating');
        }

        return $this->convertScore($raw, $format);
    }

    public function rate($value, $user_id = null)
    {
        $user_id ??= Auth::id();
        return $this->ratings()->updateOrCreate(
            ['user_id' => $user_id],
            ['rating' => $value]
        );
    }

    /**
     * Check if the song has at least one inactive artist.
     */
    public function hasInactiveArtists()
    {
        return $this->artists()->where('status', false)->exists();
    }
}
