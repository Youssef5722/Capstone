<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use App\Models\Level;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorLevelAccess
{
    /**
     * Verify the authenticated doctor is assigned to the requested {level}
     * in the currently active academic year.
     *
     * On success: binds $activeYear and $level into request attributes so
     * controllers can access them without re-querying.
     *
     * Aborts 403 on:
     *   - No active academic year
     *   - Level does not exist
     *   - Doctor not assigned to this level in the active year
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeYear = AcademicYear::active();

        if (! $activeYear) {
            abort(403, 'No active academic year.');
        }

        // Resolve the {level} route parameter to a Level model
        $levelParam = $request->route('level');
        $level = $levelParam instanceof Level
            ? $levelParam
            : Level::find($levelParam);

        if (! $level) {
            abort(404, 'Level not found.');
        }

        // Verify the doctor is assigned to this level in the active year
        $hasAccess = Auth::user()
            ->doctorAssignments()
            ->where('level_id', $level->id)
            ->where('academic_year_id', $activeYear->id)
            ->exists();

        if (! $hasAccess) {
            abort(403, 'You are not assigned to this level in the current academic year.');
        }

        // Bind resolved objects into request attributes for controllers
        $request->attributes->set('activeYear', $activeYear);
        $request->attributes->set('resolvedLevel', $level);

        return $next($request);
    }
}
