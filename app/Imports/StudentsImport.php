<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentsImport
{
    public function __construct(
        private readonly int $levelId,
        private readonly int $academicYearId,
    ) {}

    /**
     * Process all rows as a collection.
     *
     * This method does NOT wrap in a transaction — the calling Controller
     * wraps the Excel::import() call in DB::transaction() so that any
     * exception thrown here triggers a full rollback.
     *
     * PREP-2: Rows are accumulated in $batch[] and inserted in one
     * bulk Student::insert() call instead of N individual INSERTs.
     * This is significantly faster for large Excel files.
     *
     * @throws \Exception on validation or uniqueness failure
     */
    public function collection(Collection $rows): void
    {
        // PREP-2: Accumulate validated rows for a single bulk INSERT.
        $batch = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // 1-indexed, account for heading row

            // ── Required field validation ──────────────────────────────
            $name         = trim($row['name'] ?? '');
            $universityId = trim($row['university_id'] ?? '');

            if ($name === '') {
                throw new \Exception("Row {$rowNum}: 'name' is required.");
            }
            if ($universityId === '') {
                throw new \Exception("Row {$rowNum}: 'university_id' is required.");
            }
            // Note: email is intentionally NOT required here — students register
            // their own email address when they activate their account.

            // ── Duplicate check: university_id per academic year ───────
            $exists = Student::where('university_id', $universityId)
                             ->where('academic_year_id', $this->academicYearId)
                             ->exists();

            if ($exists) {
                throw new \Exception(
                    "Row {$rowNum}: University ID '{$universityId}' already exists in this academic year."
                );
            }

            // ── Unique activation_code with race-condition protection ──
            do {
                $code = Str::random(10);
            } while (Student::where('activation_code', $code)->exists());

            // ── Accumulate row for batch insert ────────────────────────
            // PREP-2: created_at/updated_at set manually — insert() bypasses
            // Eloquent model timestamps.
            $batch[] = [
                'name'             => $name,
                'university_id'    => $universityId,
                // email is null — student will set it at account activation
                'activation_code'  => $code,
                'is_active'        => false,
                'level_id'         => $this->levelId,
                'academic_year_id' => $this->academicYearId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        // ── Single bulk INSERT for all validated rows ──────────────────
        // PREP-2: One DB round-trip instead of N — much faster at scale.
        if (!empty($batch)) {
            Student::insert($batch);
        }
    }
}
