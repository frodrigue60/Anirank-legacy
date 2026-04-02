<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongReport extends Model
{
    use HasFactory;

    const STATUS_PENDING = false;
    const STATUS_FIXED = true;

    protected $fillable = [
        'user_id',
        'song_id',
        'title',
        'content',
        'source',
        'status',
    ];
    
    protected $casts = [
        'status' => 'boolean',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toggle()
    {
<<<<<<< HEAD
        $this->status = ! $this->status;
=======
        $this->status = !$this->status;
>>>>>>> origin/main
        return $this->save();
    }
}
