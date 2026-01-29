<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('create');
        });

        static::updated(function ($model) {
            $model->logActivity('update');
        });

        static::deleted(function ($model) {
            $model->logActivity('delete');
        });
    }

    public function logActivity($action)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $changes = null;

            if ($action === 'update') {
                $changes = [
                    'before' => array_intersect_key($this->getOriginal(), $this->getDirty()),
                    'after' => $this->getDirty(),
                ];
            } elseif ($action === 'create') {
                $changes = ['after' => $this->getAttributes()];
            } elseif ($action === 'delete') {
                $changes = ['before' => $this->getAttributes()];
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => $action,
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'description' => ucfirst($action) . ' activity on ' . class_basename($this) . ' #' . $this->id,
                'changes' => $changes,
                'ip_address' => request()->header('X-Forwarded-For')
                    ? explode(',', request()->header('X-Forwarded-For'))[0]
                    : request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
