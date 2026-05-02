<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DoctorAssignment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user();

        $activeYear = AcademicYear::active();

        $assignments = DoctorAssignment::with([
            'level',
            'level.students' => fn($q) => $q->where('academic_year_id', optional($activeYear)->id),
        ])
            ->where('doctor_id', $doctor->id)
            ->where('academic_year_id', optional($activeYear)->id)
            ->get();

        return view('doctor.dashboard', compact('doctor', 'activeYear', 'assignments'));
    }
}
