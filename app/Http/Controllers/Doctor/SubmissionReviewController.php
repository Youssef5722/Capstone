<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Submission;
use App\Models\Task;
use App\Models\Workspace;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionReviewController extends Controller
{
    public function __construct(private SubmissionService $submissionService) {}

    public function approve(Request $request, Level $level, Workspace $workspace, Task $task, Submission $submission)
    {
        $this->submissionService->approve($submission, Auth::user());

        return redirect()
            ->route('doctor.tasks.show', [$level, $workspace, $task])
            ->with('success', __('cms.submissions.approved_success'));
    }

    public function reject(Request $request, Level $level, Workspace $workspace, Task $task, Submission $submission)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $this->submissionService->reject($submission, Auth::user(), $request->rejection_reason);

        return redirect()
            ->route('doctor.tasks.show', [$level, $workspace, $task])
            ->with('success', __('cms.submissions.rejected_success'));
    }
}
