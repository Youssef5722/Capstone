<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AcademicYear;

class EnsureStudentYearActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $student = auth('student')->user();

        if (!$student) {
            return redirect()->route('student.login');
        }

        $activeYear = AcademicYear::active();

        if (!$activeYear || $student->academic_year_id !== $activeYear->id) {
            auth('student')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('student.login')
                ->withErrors(['email' => __('cms.student.year_not_active')]);
        }

        return $next($request);
    }
}
