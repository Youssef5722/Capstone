<?php

namespace App\Services;

use App\Models\Student;

class StudentService
{
    /**
     * Return students scoped by level and academic year, with optional activation filter.
     */
    public function getStudents(int $levelId, int $yearId, ?string $filter = null)
    {
        $query = Student::where('level_id', $levelId)
                        ->where('academic_year_id', $yearId);

        if ($filter === 'activated') {
            $query->where('is_active', true);
        } elseif ($filter === 'not_activated') {
            $query->where('is_active', false);
        }

        return $query->orderBy('name')->get()->makeVisible('activation_code');
    }

    /**
     * Soft-delete a student (sets deleted_at — never force-deletes).
     */
    public function deleteStudent(Student $student): void
    {
        $student->delete(); // SoftDeletes trait — sets deleted_at
    }
}
