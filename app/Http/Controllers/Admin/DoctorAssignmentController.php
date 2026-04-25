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
    public function showAssignForm($id)
    {
        $doctor = User::query()->whereHas('role', function($q) {
            $q->where('name', 'doctor');
        })->where('id', $id)->firstOrFail();

        if ($doctor->status !== 'approved') {
            return redirect()->route('admin.doctors.index')
                ->with('error', __('cms.doctors.cannot_assign_unapproved'));
        }

        $levels = Level::all();
        $year   = AcademicYear::where('is_active', true)->first();

        $assignedLevelIds = DoctorAssignment::where('doctor_id', $doctor->id)
            ->where('academic_year_id', optional($year)->id)
            ->pluck('level_id')
            ->toArray();

        return view('admin.doctors.assign', compact('doctor', 'levels', 'year', 'assignedLevelIds'));
    }

    public function assign(AssignDoctorRequest $request, $id)
    {
        $doctor = User::query()->whereHas('role', function($q) {
            $q->where('name', 'doctor');
        })->where('id', $id)->firstOrFail();

        if ($doctor->status !== 'approved') {
            return redirect()->back()
                ->with('error', __('cms.doctors.cannot_assign_unapproved'));
        }

        $year = AcademicYear::where('is_active', true)->first();
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
                    'doctor_id'          => $doctor->id,
                    'level_id'           => $levelId,
                    'academic_year_id'   => $year->id,
                ]);
            }
        });

        return redirect()->route('admin.doctors.assignments.show', $doctor->id)
            ->with('success', __('cms.assignments.success'));
    }

    public function show($id)
    {
        $doctor = User::query()->whereHas('role', function($q) {
            $q->where('name', 'doctor');
        })->where('id', $id)->firstOrFail();

        $year = AcademicYear::where('is_active', true)->first();

        $assignments = DoctorAssignment::with('level')
            ->where('doctor_id', $doctor->id)
            ->where('academic_year_id', optional($year)->id)
            ->get();

        return view('admin.doctors.assignments', compact('doctor', 'assignments', 'year'));
    }
}
