<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory, \App\Traits\HasImages;

    protected $fillable = [
        'name',
        'description',
        'is_active'
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
        $image = $this->images->where('type', 'icon')->first();
        return $image ? \Illuminate\Support\Facades\Storage::disk($image->disk)->url($image->path) : asset('img/placeholders/default-badge.webp');
    }
}
