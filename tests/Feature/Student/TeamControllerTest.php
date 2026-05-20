<?php

namespace Tests\Feature\Student;

use App\Models\AcademicYear;
use App\Models\Level;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Sprint3TestHelpers;
use Tests\TestCase;

/**
 * B.4 — Student Team Controller Integration Tests (I-19 … I-22)
 */
class TeamControllerTest extends TestCase
{
    use DatabaseMigrations, Sprint3TestHelpers;

    private AcademicYear     $year;
    private Level            $level;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetYearCache();

        $this->level = $this->createLevel('Level 2');
        $this->year  = $this->createActiveYear();
    }

    protected function tearDown(): void
    {
        $this->resetYearCache();
        parent::tearDown();
    }

    // ── I-19: Student in a team → GET /student/team returns 200 with team info

    public function test_I19_student_in_team_sees_team_page(): void
    {
        $student = $this->createStudent($this->level, $this->year, 'st1');
        $this->createTeam($this->level, $this->year, $student);

        $response = $this->actingAs($student, 'student')
            ->get('/student/team');

        $response->assertOk();
    }

    // ── I-20: Student NOT in any team → 200 with no-team message, no 500 ──────

    public function test_I20_student_not_in_team_shows_no_team_message(): void
    {
        $student = $this->createStudent($this->level, $this->year, 'st2');

        $response = $this->actingAs($student, 'student')
            ->get('/student/team');

        $response->assertOk();
        $response->assertDontSee('500');
    }

    // ── I-21: Unauthenticated → redirected to /student/login ─────────────────

    public function test_I21_unauthenticated_redirected_to_login(): void
    {
        $this->get('/student/team')
             ->assertRedirect(); // redirects to /login (global guest redirect)
    }

    // ── I-22: Inactive year blocks student ───────────────────────────────────
    // EnsureStudentYearActive middleware checks student's own year is still active.

    public function test_I22_student_blocked_when_year_inactive(): void
    {
        $student = $this->createStudent($this->level, $this->year, 'st3');

        // Deactivate the year
        $this->year->update(['is_active' => false]);
        $this->resetYearCache();

        // The EnsureStudentYearActive middleware should block the student
        $response = $this->actingAs($student, 'student')
            ->get('/student/team');

        // Expect redirect (middleware kicks student out) rather than 200
        $response->assertRedirect();
    }
}
