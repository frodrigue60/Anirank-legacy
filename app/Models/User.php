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
    use HasApiTokens, HasFactory, Notifiable;
    protected $appends = ['avatar_url', 'banner_url'];

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

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
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
            return \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->avatar);
        }

        $name = isset($this->name) ? urlencode($this->name) : 'User';
        return "https://ui-avatars.com/api/?name={$name}&color=fff&background=random";
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->banner) : null;
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
}
