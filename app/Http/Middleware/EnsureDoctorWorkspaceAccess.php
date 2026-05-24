<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorWorkspaceAccess
{
    /**
     * Verify the {workspace} belongs to a team in the doctor's assigned level
     * for the currently active academic year.
     *
     * Pre-conditions (guaranteed by preceding middleware in the stack):
     *  - doctor.level has already run: $request->attributes has 'resolvedLevel' and 'activeYear'
     *
     * On success: binds 'resolvedWorkspace' into request attributes.
     * Aborts 403 if the workspace does not belong to this level + year.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $level      = $request->attributes->get('resolvedLevel');
        $activeYear = $request->attributes->get('activeYear');

        // Resolve the {workspace} route parameter
        $workspaceParam = $request->route('workspace');
        $workspace = $workspaceParam instanceof Workspace
            ? $workspaceParam
            : Workspace::find($workspaceParam);

        if (! $workspace) {
            abort(404, __('cms.workspace.not_found'));
        }

        // Verify the workspace belongs to the doctor's current level + year
        if ($workspace->level_id !== $level->id || $workspace->academic_year_id !== $activeYear->id) {
            abort(403, __('cms.workspace.access_denied'));
        }

        // Bind for controllers to use without re-querying
        $request->attributes->set('resolvedWorkspace', $workspace);

        return $next($request);
    }
}
