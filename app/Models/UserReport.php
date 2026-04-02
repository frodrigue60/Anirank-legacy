<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'reported_user_id',
        'reporter_user_id',
        'source',
        'reason',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reporterUser()
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }
}
