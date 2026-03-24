<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRequest;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, \App\Traits\Auditable;
    protected $appends = ['avatar_url', 'banner_url', 'xp_progress', 'level_name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'score_format_id',
        'slug',
        'profile_color',
        'about',
        'avatar',
        'banner',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userRequests()
    {
        return $this->hasMany(UserRequest::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }

        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }

    public function isStaff()
    {
        return $this->hasRole(['admin', 'editor', 'creator']);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isEditor()
    {
        return $this->hasRole('editor');
    }

    public function isCreator()
    {
        return $this->hasRole('creator');
    }

    public function generateSlug()
    {
        $slug = Str::slug($this->name); // Genera el slug a partir del nombre
        $originalSlug = $slug;
        $count = 1;

        // Verifica si el slug ya existe y agrega un sufijo numérico si es necesario
        while (User::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $this->slug = $slug;
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function favoriteArtists()
    {
        return $this->belongsToMany(Artist::class, 'artist_user')->withTimestamps();
    }
    
    public function favoriteSongs()
    {
        return $this->belongsToMany(Song::class, 'song_user')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /* PLAYLISTS */
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Obtener playlists con conteo de animes (para eficiencia)
     */
    public function playlistsWithCount()
    {
        return $this->playlists()->withCount('songs');
    }

    /**
     * Obtener playlists públicas de otros usuarios
     */
    public function publicPlaylists()
    {
        return Playlist::where('is_public', true)
            ->where('user_id', '!=', $this->id)
            ->with('user')
            ->withCount('songs');
    }

    /**
     * Verificar si el usuario puede ver una playlist específica
     */
    public function canViewPlaylist(Playlist $playlist)
    {
        return $playlist->user_id === $this->id || $playlist->is_public;
    }

    /**
     * Obtener el número total de playlists del usuario
     */
    public function getPlaylistsCountAttribute()
    {
        return $this->playlists()->count();
    }

    /**
     * Obtener el número total de animes en todas las playlists del usuario
     */
    public function getTotalPlaylistSongsAttribute()
    {
        return $this->playlists()->withCount('songs')->get()->sum('songs_count');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->generateSlug();
            }
        });

        static::deleting(function ($user) {
            $disk = env('FILESYSTEM_DISK', 'public');
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk($disk)->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($user->avatar);
            }
            if ($user->banner && \Illuminate\Support\Facades\Storage::disk($disk)->exists($user->banner)) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($user->banner);
            }
        });
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) return $this->avatar;
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }

        $name = isset($this->name) ? urlencode($this->name) : 'User';
        return "https://ui-avatars.com/api/?name={$name}&color=fff&background=random";
    }

    public function getBannerUrlAttribute()
    {
        if (!$this->banner) return null;
        if (filter_var($this->banner, FILTER_VALIDATE_URL)) return $this->banner;
        return \Illuminate\Support\Facades\Storage::url($this->banner);
    }

    /**
     * Update or create a specific type of image (avatar or banner).
     */
    public function updateOrCreateImage(string $path, string $type)
    {
        $disk = config('filesystems.default');
        $oldPath = $this->{$type};

        if ($oldPath && \Illuminate\Support\Facades\Storage::disk($disk)->exists($oldPath)) {
            \Illuminate\Support\Facades\Storage::disk($disk)->delete($oldPath);
        }

        $this->update([
            $type => $path
        ]);

        return $this;
    }

    /**
     * Update the user's last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        return $this->save();
    }

    /**
     * The badges that belong to the user.
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class)
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function scoreFormat()
    {
        return $this->belongsTo(ScoreFormat::class, 'score_format_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Users that are following this user.
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * Users that this user is following.
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * Check if the user is following another user.
     */
    public function isFollowing(User $user)
    {
        return $this->following()->where('followed_id', $user->id)->exists();
    }

    /**
     * Get notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Follow a user.
     */
    public function follow(User $user)
    {
        if ($this->id === $user->id) {
            return;
        }

        $res = $this->following()->syncWithoutDetaching([$user->id]);

        // Trigger notification if it's a new follow
        if (!empty($res['attached'])) {
            $user->notifications()->create([
                'type' => 'follow',
                'subject_id' => $this->id,
                'subject_type' => 'user',
                'data' => [
                    'follower_id' => $this->id,
                    'follower_name' => $this->name,
                    'follower_avatar' => $this->avatar_url,
                    'message' => "{$this->name} started following you",
                ],
            ]);
        }

        return $res;
    }

    /**
     * Unfollow a user.
     */
    public function unfollow(User $user)
    {
        return $this->following()->detach($user->id);
    }

    /**
     * Toggle follow state for a user.
     */
    public function toggleFollow(User $user)
    {
        if ($this->isFollowing($user)) {
            return $this->unfollow($user);
        }

        return $this->follow($user);
    }

    /**
     * Get the user's current level model.
     */
    public function getCurrentLevelAttribute()
    {
        return Level::where('level', $this->level)->first();
    }

    /**
     * Get the user's next level model.
     */
    public function getNextLevelAttribute()
    {
        return Level::where('level', $this->level + 1)->first();
    }

    /**
     * Get the XP progression percentage towards the next level.
     */
    public function getXpProgressAttribute()
    {
        $currentLevel = $this->current_level;
        $nextLevel = $this->next_level;

        if (!$nextLevel) {
            return 100; // Max level reached
        }

        $minXp = $currentLevel ? $currentLevel->min_xp : 0;
        $maxXp = $nextLevel->min_xp;

        if ($maxXp <= $minXp) {
            return 100;
        }

        $progress = (($this->xp - $minXp) / ($maxXp - $minXp)) * 100;

        return min(100, max(0, $progress));
    }

    /**
     * Get the level name or a default.
     */
    public function getLevelNameAttribute()
    {
        $level = $this->current_level;
        return $level ? ($level->name ?? "Level {$this->level}") : "Level {$this->level}";
    }
}
