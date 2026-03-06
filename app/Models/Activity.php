<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'target_id',
        'target_type',
        'action_value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->morphTo('target', 'target_type', 'target_id');
    }

    /**
     * Helper to log an activity
     */
    public static function log($userId, $actionType, $targetId, $targetType, $value = null)
    {
        return self::create([
            'user_id' => $userId,
            'action_type' => $actionType,
            'target_id' => $targetId,
            'target_type' => $targetType,
            'action_value' => $value
        ]);
    }
}
