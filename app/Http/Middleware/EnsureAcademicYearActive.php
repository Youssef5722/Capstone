<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAcademicYearActive
{
    /**
     * Block access to any guarded route when no academic year is active.
     *
     * Routes excluded from this middleware:
     *   - All login / register / logout routes
     *   - All academic-year management routes (index, create, store, activate, destroy)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! AcademicYear::active()) {
            return redirect()
                ->route('admin.academic-years.index')
                ->with('error', __('cms.academic_years.no_active_year_blocked'));
        }

        return $next($request);
    }
}
