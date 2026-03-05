<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'icon',
    ];

    /**
     * The users that belong to the badge.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    /**
     * Get the badge's icon.
     */
    public function getIconUrlAttribute()
    {
        if (!$this->icon) return null;
        if (filter_var($this->icon, FILTER_VALIDATE_URL)) return $this->icon;
        return \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->icon);
    }
}
