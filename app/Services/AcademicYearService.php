<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class AcademicYearService
{
    /**
     * Return the currently active academic year.
     * Throws ModelNotFoundException if none is active.
     */
    public function getActiveYear(): AcademicYear
    {
        return AcademicYear::where('is_active', true)->firstOrFail();
    }

    /**
     * Activate the given year inside a transaction.
     * All other years are deactivated first, so we can never end up
     * with zero active years if the second UPDATE fails.
     *
     * PREP-1: Accepts the AcademicYear model directly — the Controller
     * resolves it via Route Model Binding, so findOrFail() is no longer
     * needed here.
     */
    public function activateYear(AcademicYear $academicYear): void
    {
        DB::transaction(function () use ($academicYear) {
            // 1. Deactivate every year
            AcademicYear::query()->update(['is_active' => false]);

            // 2. Activate only the target year using the already-resolved model
            $academicYear->update(['is_active' => true]);

            // Note: related data (students, doctor_assignments, project_ideas)
            // is intentionally left untouched — it remains as historical data.
        });
    }

    /**
     * Return true if the year is safe to delete (no related data).
     * Returns false when ANY of: students, doctor_assignments, or
     * project_ideas rows still reference this year.
     */
    public function canDelete(AcademicYear $year): bool
    {
        if ($year->doctorAssignments()->exists()) {
            return false;
        }

        if ($year->students()->exists()) {
            return false;
        }

        if ($year->projectIdeas()->exists()) {
            return false;
        }

        return true;
    }
}
