<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Task;
use App\Models\Workspace;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function create(Request $request, Level $level, Workspace $workspace)
    {
        $workspace->load(['team.leader', 'phases']);
        $phases = $workspace->phases;

        return view('doctor.workspaces.tasks.create', compact('level', 'workspace', 'phases'));
    }

    public function store(Request $request, Level $level, Workspace $workspace)
    {
        $workspace->load('team');

        $data = $request->validate([
            'phase_id'    => 'required|exists:phases,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high',
            'deadline'    => 'nullable|date',
        ]);

        // assigned_to is always the team leader — hard-set, not user-submitted
        $data['assigned_to'] = $workspace->team->leader_id;

        $phase = $workspace->phases()->findOrFail($data['phase_id']);

        $this->taskService->store($workspace, $phase, $data, Auth::id());

        return redirect()
            ->route('doctor.workspaces.show', [$level, $workspace])
            ->with('success', __('cms.tasks.created_success'));
    }

    public function show(Request $request, Level $level, Workspace $workspace, Task $task)
    {
        $task->load([
            'phase',
            'assignee',
            'subTasks.assignee',
            'subTasks.submissions',
            'submissions.submitter',
            'comments.commentedBy',
        ]);

        return view('doctor.workspaces.tasks.show', compact('level', 'workspace', 'task'));
    }

    public function update(Request $request, Level $level, Workspace $workspace, Task $task)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,in_progress,submitted,approved,rejected',
            'deadline'    => 'nullable|date',
        ]);

        $this->taskService->update($task, $data);

        return redirect()
            ->route('doctor.tasks.show', [$level, $workspace, $task])
            ->with('success', __('cms.tasks.updated_success'));
    }

    public function destroy(Request $request, Level $level, Workspace $workspace, Task $task)
    {
        $this->taskService->delete($task);

        return redirect()
            ->route('doctor.workspaces.show', [$level, $workspace])
            ->with('success', __('cms.tasks.deleted_success'));
    }
}
