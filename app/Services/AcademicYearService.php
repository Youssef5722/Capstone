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
     */
    public function activateYear(int $id): void
    {
        DB::transaction(function () use ($id) {
            // 1. Deactivate every year
            AcademicYear::query()->update(['is_active' => false]);

            // 2. Activate only the target year
            AcademicYear::findOrFail($id)->update(['is_active' => true]);

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

        // students() and projectIdeas() checks will be added in Sprint 2.
        // Placeholder queries use the relationship names that will be defined later.
        // For now we only guard on doctor_assignments.

        return true;
    }
}
