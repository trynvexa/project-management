<?php

namespace App\Support;

use App\Models\Task;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class WorkspaceNotifications
{
    /**
     * Legacy task notifications are resolved through their task. If that task
     * no longer exists, the notification has no safe workspace association and
     * is deliberately excluded.
     */
    public static function forWorkspace(Collection $notifications, int $workspaceId): Collection
    {
        return $notifications->filter(
            fn (DatabaseNotification $notification): bool => self::belongsToWorkspace($notification, $workspaceId)
        )->values();
    }

    public static function belongsToWorkspace(DatabaseNotification $notification, int $workspaceId): bool
    {
        $data = $notification->data;

        if (isset($data['workspace_id'])) {
            return (int) $data['workspace_id'] === $workspaceId;
        }

        $taskId = $data['task_id'] ?? null;

        return $taskId && Task::withoutGlobalScope('workspace-owner')
            ->whereKey($taskId)
            ->where('workspace_id', $workspaceId)
            ->exists();
    }
}
