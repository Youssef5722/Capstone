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
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    // ── Update ─────────────────────────────────────────────────
    // PREP-1: Route Model Binding — $academicYear resolved by Laravel.
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $academicYear->update($request->validated());

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.updated_success'));
    }

    // ── Activate ───────────────────────────────────────────────
    // PREP-1: Route Model Binding — passes resolved Model directly to Service.
    public function activate(AcademicYear $academicYear)
    {
        // Pass the model — Service no longer needs to re-fetch via findOrFail().
        $this->service->activateYear($academicYear);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.activated_success'));
    }

    // ── Destroy ────────────────────────────────────────────────
    // PREP-1: Route Model Binding — Laravel resolves and 404s automatically.
    public function destroy(AcademicYear $academicYear)
    {
        // Guard 1 — never delete the active year.
        if ($academicYear->is_active) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('cms.academic_years.cannot_delete_active'));
        }

        // Guard 2 — never delete a year that still has related data.
        if (! $this->service->canDelete($academicYear)) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('cms.academic_years.cannot_delete_has_data'));
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('cms.academic_years.deleted_success'));
    }
}
