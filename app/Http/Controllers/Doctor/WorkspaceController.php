<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    /**
     * List all workspaces for the given level in the active year.
     */
    public function index(Request $request, Level $level)
    {
        [$resolvedLevel, $activeYear] = $this->resolveMiddlewareContext($request);

        $workspaces = Workspace::where('level_id', $resolvedLevel->id)
            ->where('academic_year_id', $activeYear->id)
            ->with(['team.leader', 'team.projectIdeas', 'team.students', 'phases', 'tasks'])
            ->get();

        return view('doctor.workspaces.index', compact('workspaces', 'level'));
    }

    /**
     * Show a single workspace dashboard.
     */
    public function show(Request $request, Level $level, Workspace $workspace)
    {
        [$resolvedLevel, $activeYear] = $this->resolveMiddlewareContext($request);

        $workspace->load([
            'team.leader',
            'team.students',
            'team.projectIdeas',
            'phases.tasks.submissions',
            'tasks.phase',
            'tasks.assignee',
            'tasks.submissions',
        ]);

        // Aggregate stats
        $totalTasks    = $workspace->tasks->count();
        $doneTasks     = $workspace->tasks->whereIn('status', ['approved'])->count();
        $pendingTasks  = $workspace->tasks->where('status', 'pending')->count();

        return view('doctor.workspaces.show', compact(
            'workspace', 'level', 'totalTasks', 'doneTasks', 'pendingTasks'
        ));
    }
}
