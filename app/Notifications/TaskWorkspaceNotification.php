<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class TaskWorkspaceNotification extends Notification
{
    public function __construct(
        private readonly Task $task,
        private readonly string $event,
        private readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'category' => 'tasks',
            'event' => $this->event,
            'title' => $this->event === 'assigned' ? 'Task assigned to you' : 'Task status updated',
            'message' => $this->message,
            'workspace_id' => $this->task->workspace_id,
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'url' => route('tasks.show', $this->task),
        ]);
    }
}
