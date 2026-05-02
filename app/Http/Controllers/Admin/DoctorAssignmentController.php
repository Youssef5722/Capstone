<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\AcademicYear;
use App\Models\DoctorAssignment;
use App\Http\Requests\AssignDoctorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorAssignmentController extends Controller
{
    // ── Private helper ─────────────────────────────────────────────────────────

    /**
     * Find a doctor user by ID, eager-loading their role.
     * Throws ModelNotFoundException if the user is not a doctor.
     */
    private function findDoctor(int|string $id): User
    {
        return User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'doctor'))
            ->findOrFail($id);
    }

    // ── Show Assign Form ───────────────────────────────────────────────────────

    public function showAssignForm($id)
    {
        $doctor = $this->findDoctor($id);

        if ($doctor->status !== 'approved') {
            return redirect()->route('admin.doctors.index')
                ->with('error', __('cms.doctors.cannot_assign_unapproved'));
        }

        $levels = Level::all();
        $year   = AcademicYear::active();

        $assignedLevelIds = DoctorAssignment::where('doctor_id', $doctor->id)
            ->where('academic_year_id', optional($year)->id)
            ->pluck('level_id')
            ->toArray();

        return view('admin.doctors.assign', compact('doctor', 'levels', 'year', 'assignedLevelIds'));
    }

    // ── Assign ─────────────────────────────────────────────────────────────────

    public function assign(AssignDoctorRequest $request, $id)
    {
        $doctor = $this->findDoctor($id);

        if ($doctor->status !== 'approved') {
            return redirect()->back()
                ->with('error', __('cms.doctors.cannot_assign_unapproved'));
        }

        $year = AcademicYear::active();
        if (!$year) {
            return redirect()->back()
                ->with('error', __('cms.assignments.no_active_year'));
        }

        DB::transaction(function () use ($doctor, $year, $request) {
            DoctorAssignment::where('doctor_id', $doctor->id)
                ->where('academic_year_id', $year->id)
                ->delete();

            foreach ($request->levels as $levelId) {
                DoctorAssignment::create([
                    'doctor_id'        => $doctor->id,
                    'level_id'         => $levelId,
                    'academic_year_id' => $year->id,
                ]);
            }
        });

        return redirect()->route('admin.doctors.assignments.show', $doctor->id)
            ->with('success', __('cms.assignments.success'));
    }

    // ── Show Assignments ───────────────────────────────────────────────────────

    public function show($id)
    {
        $doctor = $this->findDoctor($id);

        $year = AcademicYear::active();

        $assignments = DoctorAssignment::with('level')
            ->where('doctor_id', $doctor->id)
            ->where('academic_year_id', optional($year)->id)
            ->get();

        return view('admin.doctors.assignments', compact('doctor', 'assignments', 'year'));
    }
}
