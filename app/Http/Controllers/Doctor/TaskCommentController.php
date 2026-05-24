<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    /**
     * Store a comment on a Task (from doctor).
     */
    public function store(Request $request, Level $level, Workspace $workspace, Task $task)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        TaskComment::create([
            'commentable_id'    => $task->id,
            'commentable_type'  => Task::class,
            'commented_by_id'   => Auth::id(),
            'commented_by_type' => User::class,
            'comment'           => $request->comment,
        ]);

        return redirect()
            ->route('doctor.tasks.show', [$level, $workspace, $task])
            ->with('success', __('cms.comments.posted_success'));
    }
}
