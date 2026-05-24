<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use App\Models\Team;
use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;

class EnsureStudentInTeam
{
    /**
     * Verify the authenticated student belongs to a team that has a workspace
     * in the currently active academic year.
     *
     * On success: binds 'studentTeam' and 'studentWorkspace' into request attributes.
     * On failure: redirects to student dashboard with error flash.
     */
    public function handle(Request $request, Closure $next)
    {
        $student    = auth('student')->user();
        $activeYear = AcademicYear::active();

        if (! $student || ! $activeYear) {
            return redirect()->route('student.dashboard')
                ->with('error', __('cms.workspace.no_active_year'));
        }

        // Find the team this student belongs to in the active year
        $team = Team::whereHas('students', fn ($q) =>
                        $q->where('students.id', $student->id)
                          ->where('team_student.academic_year_id', $activeYear->id)
                    )
                    ->where('academic_year_id', $activeYear->id)
                    ->with('workspace')
                    ->first();

        if (! $team) {
            return redirect()->route('student.dashboard')
                ->with('error', __('cms.workspace.no_team'));
        }

        $workspace = $team->workspace;

        if (! $workspace) {
            return redirect()->route('student.dashboard')
                ->with('error', __('cms.workspace.not_created_yet'));
        }

        // Bind for controllers
        $request->attributes->set('studentTeam', $team);
        $request->attributes->set('studentWorkspace', $workspace);

        return $next($request);
    }
}
