<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XpLog extends Model
{
    protected $fillable = [
        'user_id',
        'xp_activity_id',
        'xp_amount',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user that gained the XP.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity that triggered the XP gain.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(XpActivity::class, 'xp_activity_id');
    }
}
