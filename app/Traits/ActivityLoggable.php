<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait ActivityLoggable
{
    public static function bootActivityLoggable()
    {
        static::created(function ($model) {
            $model->logActivity('create', 'Created new record', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            $model->logActivity('update', 'Updated record', $original, $changes);
        });

        static::deleted(function ($model) {
            $model->logActivity('delete', 'Deleted record', $model->getAttributes(), null);
        });
    }

    protected function logActivity($action, $description, $oldValues = null, $newValues = null)
    {
        $userId = Auth::id();

        $changes = null;
        if ($oldValues && $newValues) {
            $changes = [
                'old' => $oldValues,
                'new' => $newValues,
            ];
        } elseif ($newValues) {
            $changes = [
                'new' => $newValues,
            ];
        }

        try {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'model_type' => get_class($this),
                'model_id' => (string) $this->getKey(),
                'description' => $description,
                'changes' => $changes,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't fail the operation
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
