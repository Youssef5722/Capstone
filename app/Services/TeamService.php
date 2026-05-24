<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeamService
{
    /**
     * Create a new team for the given level + year.
     * Validates the leader belongs to that level and year.
     */
    public function createTeam(array $data, Level $level, AcademicYear $year): Team
    {
        return DB::transaction(function () use ($data, $level, $year) {
            $leader = Student::where('id', $data['leader_id'])
                             ->where('level_id', $level->id)
                             ->where('academic_year_id', $year->id)
                             ->firstOrFail();

            $team = Team::create([
                'name'             => $data['name'] ?? null,
                'leader_id'        => $leader->id,
                'level_id'         => $level->id,
                'academic_year_id' => $year->id,
            ]);

            // Attach the provided student IDs (includes leader)
            if (!empty($data['student_ids'])) {
                $this->addStudents($team, $data['student_ids']);
            }

            return $team;
        });
    }

    /**
     * Attach students to an existing team.
     * Validates: same level, same year, not already in a team this year.
     */
    public function addStudents(Team $team, array $studentIds): void
    {
        // Fix 2: workspace locks team membership
        if ($team->workspace()->exists()) {
            throw ValidationException::withMessages([
                'student_ids' => [__('cms.teams.locked_by_workspace')],
            ]);
        }

        DB::transaction(function () use ($team, $studentIds) {
            foreach ($studentIds as $studentId) {
                $student = Student::where('id', $studentId)
                                  ->where('level_id', $team->level_id)
                                  ->where('academic_year_id', $team->academic_year_id)
                                  ->first();

                if (! $student) {
                    throw ValidationException::withMessages([
                        'student_ids' => [__('cms.teams.student_wrong_level')],
                    ]);
                }

                // Check not already in a team this year
                $alreadyAssigned = $student->teams()
                    ->where('team_student.academic_year_id', $team->academic_year_id)
                    ->exists();

                if ($alreadyAssigned) {
                    throw ValidationException::withMessages([
                        'student_ids' => [__('cms.teams.student_already_assigned', ['name' => $student->name])],
                    ]);
                }

                $team->students()->attach($studentId, [
                    'academic_year_id' => $team->academic_year_id,
                ]);
            }
        });
    }

    /**
     * Remove a student from a team.
     * Aborts 422 if the student is the current leader — must reassign leader first.
     */
    public function removeStudent(Team $team, Student $student): void
    {
        // Fix 2: workspace locks team membership
        if ($team->workspace()->exists()) {
            throw ValidationException::withMessages([
                'student_ids' => [__('cms.teams.locked_by_workspace')],
            ]);
        }

        if ($student->id === $team->leader_id) {
            abort(422, __('cms.teams.cannot_remove_leader'));
        }

        $team->students()->detach($student->id);
    }

    /**
     * Change the team leader.
     * Validates the new leader is already a member of the team.
     */
    public function setLeader(Team $team, Student $student): void
    {
        $isMember = $team->students()
                         ->where('team_student.academic_year_id', $team->academic_year_id)
                         ->where('students.id', $student->id)
                         ->exists();

        if (! $isMember) {
            abort(422, __('cms.teams.leader_not_member'));
        }

        $team->update(['leader_id' => $student->id]);
    }

    /**
     * Delete a team — cascade on FKs handles team_student, team_project, team_requests.
     */
    public function deleteTeam(Team $team): void
    {
        DB::transaction(function () use ($team) {
            $team->delete();
        });
    }

    /**
     * Transfer a student from one team to another in the same year.
     * Fix 10: guard against workspace lock on source team.
     */
    public function transferStudent(Student $student, Team $fromTeam, Team $toTeam): void
    {
        // Cannot transfer if source team has a workspace
        if ($fromTeam->workspace()->exists()) {
            throw ValidationException::withMessages([
                'student_ids' => [__('cms.teams.transfer_blocked_workspace')],
            ]);
        }

        DB::transaction(function () use ($student, $fromTeam, $toTeam) {
            $fromTeam->students()->detach($student->id);
            $toTeam->students()->attach($student->id, [
                'academic_year_id' => $toTeam->academic_year_id,
            ]);
        });
    }
}
