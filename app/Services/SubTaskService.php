<?php

namespace App\Services;

use App\Models\Student;
use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Validation\ValidationException;

class SubTaskService
{
    /**
     * Create a sub-task under a parent task.
     * Hard validation: only the team leader (task->assignee) may create sub-tasks.
     */
    public function store(Task $task, array $data, Student $requester): SubTask
    {
        // Only the team leader (who is the task's assignee) can create sub-tasks
        if ($requester->id !== $task->assigned_to) {
            abort(403, __('cms.subtasks.leader_only'));
        }

        return SubTask::create(array_merge($data, [
            'task_id' => $task->id,
            'status'  => 'pending',
        ]));
    }

    public function update(SubTask $subTask, array $data): SubTask
    {
        $subTask->update($data);
        return $subTask->fresh();
    }

    public function delete(SubTask $subTask, Student $requester): void
    {
        // Only the leader can delete sub-tasks
        if ($requester->id !== $subTask->task->assigned_to) {
            abort(403, __('cms.subtasks.leader_only'));
        }
        $subTask->delete();
    }

    public function changeStatus(SubTask $subTask, string $status): void
    {
        $subTask->update(['status' => $status]);
    }
}
