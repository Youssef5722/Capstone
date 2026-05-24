<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SubTask;
use App\Models\Task;
use App\Services\SubTaskService;
use Illuminate\Http\Request;

class SubTaskController extends Controller
{
    public function __construct(private SubTaskService $subTaskService) {}

    /**
     * Leader creates a sub-task under a task.
     */
    public function store(Request $request, Task $task)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');

        // Verify the task belongs to this workspace
        if ($task->workspace_id !== $workspace->id) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:students,id',
            'deadline'    => 'nullable|date',
        ]);

        $this->subTaskService->store($task, $data, $student);

        return redirect()
            ->route('student.workspace.tasks.show', $task)
            ->with('success', __('cms.subtasks.created_success'));
    }

    /**
     * Leader updates a sub-task.
     */
    public function update(Request $request, Task $task, SubTask $subTask)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');

        if ($task->workspace_id !== $workspace->id || $subTask->task_id !== $task->id) {
            abort(403);
        }

        // Only leader can update
        if ($student->id !== $task->assigned_to) {
            abort(403, __('cms.subtasks.leader_only'));
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:students,id',
            'deadline'    => 'nullable|date',
            'status'      => 'required|in:pending,in_progress,submitted,approved,rejected',
        ]);

        $this->subTaskService->update($subTask, $data);

        return redirect()
            ->route('student.workspace.tasks.show', $task)
            ->with('success', __('cms.subtasks.updated_success'));
    }

    /**
     * Leader deletes a sub-task.
     */
    public function destroy(Request $request, Task $task, SubTask $subTask)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');

        if ($task->workspace_id !== $workspace->id || $subTask->task_id !== $task->id) {
            abort(403);
        }

        $this->subTaskService->delete($subTask, $student);

        return redirect()
            ->route('student.workspace.tasks.show', $task)
            ->with('success', __('cms.subtasks.deleted_success'));
    }
}
