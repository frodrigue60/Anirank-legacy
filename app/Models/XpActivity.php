<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class XpActivity extends Model
{
    protected $fillable = [
        'key',
        'xp_amount',
        'description',
        'cooldown_seconds',
    ];

    /**
     * Get the logs for this activity.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(XpLog::class);
    }
}
