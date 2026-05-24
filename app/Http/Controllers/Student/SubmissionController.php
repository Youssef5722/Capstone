<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SubTask;
use App\Models\Task;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(private SubmissionService $submissionService) {}

    /**
     * Polymorphic upload — handles Task submissions (leader) and SubTask submissions (member).
     *
     * Route parameter 'type' determines the submittable model: 'task' or 'subtask'.
     * Route parameter 'id' is the Task or SubTask ID.
     */
    public function store(Request $request)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');
        $team      = $request->attributes->get('studentTeam');

        $request->validate([
            'submittable_type' => 'required|in:task,subtask',
            'submittable_id'   => 'required|integer',
            'file'             => 'required|file|max:20480', // 20MB max
        ]);

        $type = $request->input('submittable_type');
        $id   = $request->input('submittable_id');

        if ($type === 'task') {
            $submittable = Task::where('id', $id)
                ->where('workspace_id', $workspace->id)
                ->where('assigned_to', $student->id) // Leader only
                ->firstOrFail();
        } else {
            $submittable = SubTask::where('id', $id)
                ->whereHas('task', fn ($q) => $q->where('workspace_id', $workspace->id))
                ->where('assigned_to', $student->id) // The specific member
                ->firstOrFail();
        }

        $this->submissionService->upload($submittable, $student, $request->file('file'));

        $redirectRoute = $type === 'task'
            ? route('student.workspace.tasks.show', $submittable->id)
            : route('student.workspace.subtasks.show', [$submittable->task_id, $submittable->id]);

        return redirect($redirectRoute)
            ->with('success', __('cms.submissions.uploaded_success'));
    }
}
