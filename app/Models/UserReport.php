<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\HasUuid;

class UserReport extends Model
{
    use Auditable, HasUuid;
    
    const STATUS_PENDING  = false;
    const STATUS_RESOLVED = true;

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

    /**
     * Get the user who was reported.
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Get the user who submitted the report.
     */
    public function reporterUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    /**
     * Toggles the status of the report.
     */
    public function toggle(): bool
    {
        $this->status = !$this->status;
        return $this->save();
    }
}
