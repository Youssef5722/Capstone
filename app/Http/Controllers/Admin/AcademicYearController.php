<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;

class AcademicYearController extends Controller
{
    public function __construct(protected AcademicYearService $service) {}

    // ── Index ──────────────────────────────────────────────────
    public function index()
    {
        $academicYears = AcademicYear::all();
        return view('admin.academic-years.index', compact('academicYears'));
    }

    // ── Create ─────────────────────────────────────────────────
    public function create()
    {
        return view('admin.academic-years.create');
    }

    // ── Store ──────────────────────────────────────────────────
    public function store(StoreAcademicYearRequest $request)
    {
        // ValidationRequest already enforces unique:academic_years,name.
        // We always force is_active = false regardless of any request input.
        AcademicYear::create($request->validated() + ['is_active' => false]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.created_success'));
    }

    // ── Edit ───────────────────────────────────────────────────
    public function edit($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    // ── Update ─────────────────────────────────────────────────
    public function update(UpdateAcademicYearRequest $request, $id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update($request->validated());

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.updated_success'));
    }

    // ── Activate ───────────────────────────────────────────────
    public function activate($id)
    {
        $this->service->activateYear((int) $id);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.activated_success'));
    }

    // ── Destroy ────────────────────────────────────────────────
    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);

        // Guard 1 — never delete the active year.
        if ($year->is_active) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('cms.academic_years.cannot_delete_active'));
        }

        // Guard 2 — never delete a year that still has related data.
        if (! $this->service->canDelete($year)) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('cms.academic_years.cannot_delete_has_data'));
        }

        $year->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.deleted_success'));
    }
}
