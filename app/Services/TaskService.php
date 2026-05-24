<?php

namespace App\Services;

use App\Models\Phase;
use App\Models\Student;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Validation\ValidationException;

class TaskService
{
    /**
     * Create a new task in the workspace.
     *
     * Hard validation (per SOP §11): assigned_to MUST be the team leader.
     */
    public function store(Workspace $workspace, Phase $phase, array $data, int $doctorId): Task
    {
        // Hard validation: ensure assigned_to is the team leader
        $this->assertAssignedToLeader($workspace, $data['assigned_to']);

        return Task::create(array_merge($data, [
            'workspace_id' => $workspace->id,
            'phase_id'     => $phase->id,
            'created_by'   => $doctorId,
            'status'       => 'pending',
        ]));
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function changeStatus(Task $task, string $status): void
    {
        $task->update(['status' => $status]);
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    /**
     * Abort 422 if the student being assigned is not the team leader.
     */
    private function assertAssignedToLeader(Workspace $workspace, int $studentId): void
    {
        $leaderId = $workspace->team->leader_id;

        if ($studentId !== $leaderId) {
            throw ValidationException::withMessages([
                'assigned_to' => [__('cms.tasks.must_assign_to_leader')],
            ]);
        }
    }
}
