<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    /**
     * Student workspace dashboard.
     *
     * Pre-conditions guaranteed by EnsureStudentInTeam middleware:
     *  - $request->attributes has 'studentTeam' and 'studentWorkspace'
     */
    public function show(Request $request)
    {
        $student   = auth('student')->user();
        $team      = $request->attributes->get('studentTeam');
        $workspace = $request->attributes->get('studentWorkspace');

        $workspace->load([
            'team.leader',
            'team.students',
            'team.projectIdeas',
            'phases.tasks',
            'tasks.phase',
            'tasks.subTasks',
            'tasks.submissions',
        ]);

        // Tasks assigned to THIS student (they are the leader, all tasks are assigned to them)
        $myTasks = $workspace->tasks
            ->where('assigned_to', $student->id);

        // Sub-tasks across all workspace tasks that are assigned to this student
        $mySubTasks = collect();
        foreach ($workspace->tasks as $task) {
            $subs = $task->subTasks->where('assigned_to', $student->id);
            foreach ($subs as $sub) {
                $mySubTasks->push($sub->setRelation('task', $task));
            }
        }

        $isLeader = $team->leader_id === $student->id;

        return view('student.workspace.show', compact(
            'workspace', 'team', 'student', 'myTasks', 'mySubTasks', 'isLeader'
        ));
    }

    /**
     * Task detail page for student.
     */
    public function showTask(Request $request, \App\Models\Task $task)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');
        $team      = $request->attributes->get('studentTeam');

        // Verify task belongs to this workspace
        if ($task->workspace_id !== $workspace->id) {
            abort(403);
        }

        $task->load([
            'phase', 'assignee',
            'subTasks.assignee', 'subTasks.submissions.submitter',
            'submissions.submitter',
            'comments.commentedBy',
        ]);

        $isLeader    = $team->leader_id === $student->id;
        $isAssignee  = $task->assigned_to === $student->id;
        $teamMembers = $workspace->team->students ?? collect();

        return view('student.workspace.tasks.show', compact(
            'workspace', 'team', 'task', 'student', 'isLeader', 'isAssignee', 'teamMembers'
        ));
    }

    /**
     * Sub-task detail page for student.
     */
    public function showSubTask(Request $request, \App\Models\Task $task, \App\Models\SubTask $subTask)
    {
        $student   = auth('student')->user();
        $workspace = $request->attributes->get('studentWorkspace');
        $team      = $request->attributes->get('studentTeam');

        if ($task->workspace_id !== $workspace->id || $subTask->task_id !== $task->id) {
            abort(403);
        }

        $subTask->load(['assignee', 'submissions.submitter', 'comments.commentedBy']);

        $isLeader    = $team->leader_id === $student->id;
        $isAssignee  = $subTask->assigned_to === $student->id;

        return view('student.workspace.subtasks.show', compact(
            'workspace', 'team', 'task', 'subTask', 'student', 'isLeader', 'isAssignee'
        ));
    }
}
