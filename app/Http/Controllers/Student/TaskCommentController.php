<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    /**
     * Store a comment on a Task or Sub-Task (from student).
     */
    public function store(Request $request)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');

        $request->validate([
            'commentable_type' => 'required|in:task,subtask',
            'commentable_id'   => 'required|integer',
            'comment'          => 'required|string|max:2000',
        ]);

        $type = $request->input('commentable_type');
        $id   = $request->input('commentable_id');

        if ($type === 'task') {
            $commentable = Task::where('id', $id)
                ->where('workspace_id', $workspace->id)
                ->firstOrFail();
            $commentableClass = Task::class;
        } else {
            $commentable = SubTask::where('id', $id)
                ->whereHas('task', fn ($q) => $q->where('workspace_id', $workspace->id))
                ->firstOrFail();
            $commentableClass = SubTask::class;
        }

        TaskComment::create([
            'commentable_id'    => $commentable->id,
            'commentable_type'  => $commentableClass,
            'commented_by_id'   => $student->id,
            'commented_by_type' => Student::class,
            'comment'           => $request->comment,
        ]);

        $redirect = $type === 'task'
            ? route('student.workspace.tasks.show', $commentable->id)
            : route('student.workspace.subtasks.show', [$commentable->task_id, $commentable->id]);

        return redirect($redirect)->with('success', __('cms.comments.posted_success'));
    }
}
