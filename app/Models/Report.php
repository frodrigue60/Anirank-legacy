<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_FIXED = 'fixed';

    protected $fillable = [
        'user_id',
        'song_id',
        'title',
        'content',
        'source',
        'status',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function toggle()
    {
        $this->status = $this->status === self::STATUS_FIXED ? self::STATUS_PENDING : self::STATUS_FIXED;
        return $this->save();
    }
}
