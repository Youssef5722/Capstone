<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SubTask;
use App\Models\Submission;
use App\Models\Task;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class SubTaskReviewController extends Controller
{
    public function __construct(private SubmissionService $submissionService) {}

    /**
     * Leader approves a sub-task submission.
     */
    public function approve(Request $request, Task $task, SubTask $subTask, Submission $submission)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');

        // Only the team leader can review sub-task submissions
        if ($student->id !== $task->assigned_to) {
            abort(403, __('cms.subtasks.leader_only'));
        }

        if ($task->workspace_id !== $workspace->id || $subTask->task_id !== $task->id) {
            abort(403);
        }

        $this->submissionService->approve($submission, $student);

        return redirect()
            ->route('student.workspace.subtasks.show', [$task->id, $subTask->id])
            ->with('success', __('cms.submissions.approved_success'));
    }

    /**
     * Leader rejects a sub-task submission with a reason.
     */
    public function reject(Request $request, Task $task, SubTask $subTask, Submission $submission)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');

        if ($student->id !== $task->assigned_to) {
            abort(403, __('cms.subtasks.leader_only'));
        }

        if ($task->workspace_id !== $workspace->id || $subTask->task_id !== $task->id) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $this->submissionService->reject($submission, $student, $request->rejection_reason);

        return redirect()
            ->route('student.workspace.subtasks.show', [$task->id, $subTask->id])
            ->with('success', __('cms.submissions.rejected_success'));
    }
}
