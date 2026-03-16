<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'title',
        'content',
        'source',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
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
        $this->status = !$this->status;
        return $this->save();
    }
}
