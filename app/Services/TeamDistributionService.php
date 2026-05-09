<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeamDistributionService
{
    /**
     * Return students in this level+year who have NO team_student row this year.
     * Scoped by both level_id AND academic_year_id per SOP §3.2.
     */
    public function getUnassigned(Level $level, AcademicYear $year): Collection
    {
        return Student::where('level_id', $level->id)
                      ->where('academic_year_id', $year->id)
                      ->whereDoesntHave('teams', fn ($q) =>
                          $q->where('team_student.academic_year_id', $year->id)
                      )
                      ->get();
    }

    /**
     * Balanced distribution: ceil(count / teamSize) teams.
     * Students assigned by index modulo total teams — no remainder.
     *
     * Example: 22 students, size 4 → 6 teams; indices distributed by $i % 6.
     */
    public function generateBalanced(Collection $students, int $teamSize): array
    {
        $total      = $students->count();
        $totalTeams = (int) ceil($total / $teamSize);

        if ($totalTeams === 0) {
            return [];
        }

        $groups = array_fill(0, $totalTeams, []);

        foreach ($students->values() as $index => $student) {
            $groups[$index % $totalTeams][] = $student;
        }

        return $groups;
    }

    /**
     * Fixed distribution: groups of exactly teamSize.
     * Remainder students returned separately — shown in preview, not auto-assigned.
     *
     * Example: 22 students, size 4 → 5 groups of 4, remainder = [2 students].
     */
    public function generateFixed(Collection $students, int $teamSize): array
    {
        $chunks = $students->chunk($teamSize);
        $groups    = [];
        $remainder = [];

        foreach ($chunks as $chunk) {
            if ($chunk->count() === $teamSize) {
                $groups[] = $chunk->values()->all();
            } else {
                $remainder = $chunk->values()->all();
            }
        }

        return ['groups' => $groups, 'remaining' => $remainder];
    }

    /**
     * Generate a preview and store it in the session (no DB writes).
     *
     * Returns ['groups' => [...], 'remaining' => [...]]
     */
    public function preview(string $mode, int $teamSize, Level $level, AcademicYear $year): array
    {
        $unassigned = $this->getUnassigned($level, $year);

        if ($mode === 'balanced') {
            $groups    = $this->generateBalanced($unassigned, $teamSize);
            $remaining = [];
        } else {
            $result    = $this->generateFixed($unassigned, $teamSize);
            $groups    = $result['groups'];
            $remaining = $result['remaining'];
        }

        // Store student IDs only in session (models are not serializable safely)
        $sessionGroups = array_map(
            fn ($group) => array_map(fn ($s) => $s->id, $group),
            $groups
        );
        $sessionRemaining = array_map(fn ($s) => $s->id, $remaining);

        session([
            'distribution.groups'    => $sessionGroups,
            'distribution.remaining' => $sessionRemaining,
            'distribution.level_id'  => $level->id,
            'distribution.year_id'   => $year->id,
            'distribution.mode'      => $mode,
            'distribution.team_size' => $teamSize,
        ]);

        return [
            'groups'    => $groups,
            'remaining' => $remaining,
        ];
    }

    /**
     * Persist the distribution stored in the session.
     * Wrapped in a single DB::transaction — any failure rolls back everything.
     * Clears the session after a successful commit.
     */
    public function confirm(array $groups, Level $level, AcademicYear $year): void
    {
        DB::transaction(function () use ($groups, $level, $year) {
            foreach ($groups as $index => $studentIds) {
                // First student in the group is auto-assigned as leader
                $leaderId = $studentIds[0];

                $team = Team::create([
                    'name'             => null, // Leaders/doctors can name via requests
                    'leader_id'        => $leaderId,
                    'level_id'         => $level->id,
                    'academic_year_id' => $year->id,
                ]);

                foreach ($studentIds as $studentId) {
                    // Double-check student isn't already assigned (race-condition guard)
                    $alreadyIn = \DB::table('team_student')
                        ->where('student_id', $studentId)
                        ->where('academic_year_id', $year->id)
                        ->exists();

                    if ($alreadyIn) {
                        // This will trigger a full rollback via the wrapping transaction
                        abort(422, __('cms.teams.distribute_conflict'));
                    }

                    $team->students()->attach($studentId, [
                        'academic_year_id' => $year->id,
                    ]);
                }
            }
        });

        // Clear session only after a successful commit
        session()->forget([
            'distribution.groups',
            'distribution.remaining',
            'distribution.level_id',
            'distribution.year_id',
            'distribution.mode',
            'distribution.team_size',
        ]);
    }
}
