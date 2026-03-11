<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'comment_id',
        'user_id',
        'title',
        'content',
        'source',
        'status',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toggle()
    {
        $this->status = ($this->status === self::STATUS_PENDING) ? self::STATUS_RESOLVED : self::STATUS_PENDING;
        return $this->save();
    }
}
