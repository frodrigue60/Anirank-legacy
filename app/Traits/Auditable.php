<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created');
        });

        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });
    }

    protected function logAudit(string $event): void
    {
        $userId = Auth::id();
        
        $old = null;
        $new = null;

        if ($event === 'updated') {
            $dirty = $this->getDirty();
            $filter = ['password', 'remember_token', 'updated_at', 'created_at', 'deleted_at'];
            $dirty = array_diff_key($dirty, array_flip($filter));

            if (empty($dirty)) {
                return;
            }

            // More robust change detection
            $actualChanges = [];
            $originalValues = [];
            foreach ($dirty as $key => $value) {
                $original = $this->getRawOriginal($key);
                if ($original != $value) {
                    $actualChanges[$key] = $value;
                    $originalValues[$key] = $original;
                }
            }

            if (empty($actualChanges)) {
                return;
            }

            $old = $originalValues;
            $new = $actualChanges;
        } elseif ($event === 'created') {
            $new = $this->getAttributes();
            $filter = ['password', 'remember_token', 'updated_at', 'created_at'];
            $new = array_diff_key($new, array_flip($filter));
        }

        AuditLog::create([
            'user_id'        => $userId,
            'event'          => $event,
            'auditable_id'   => $this->getKey(),
            'auditable_type' => $this->getMorphClass(),
            'old_values'     => $old,
            'new_values'     => $new,
            'url'            => Request::hasHeader('host') ? Request::fullUrl() : (app()->runningInConsole() ? 'console' : 'unknown'),
            'ip_address'     => Request::ip() ?? '127.0.0.1',
            'user_agent'     => Request::userAgent() ?? 'System',
            'created_at'     => now(),
        ]);
    }


    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
