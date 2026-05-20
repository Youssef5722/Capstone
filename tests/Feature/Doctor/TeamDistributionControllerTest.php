<?php

namespace Tests\Feature\Doctor;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Sprint3TestHelpers;
use Tests\TestCase;

/**
 * B.2 — Distribution Controller Integration Tests (I-07 … I-10)
 */
class TeamDistributionControllerTest extends TestCase
{
    use DatabaseMigrations, Sprint3TestHelpers;

    private AcademicYear       $year;
    private Level              $level;
    private \App\Models\User   $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetYearCache();

        $this->level  = $this->createLevel('Level 2');
        $this->year   = $this->createActiveYear();
        $this->doctor = $this->createDoctor($this->level, $this->year);
    }

    protected function tearDown(): void
    {
        $this->resetYearCache();
        parent::tearDown();
    }

    // ── I-07: Preview — stores session, teams table stays empty ──────────────

    public function test_I07_preview_stores_session_and_does_not_create_teams(): void
    {
        // Create 8 unassigned students in Level 2
        for ($i = 0; $i < 8; $i++) {
            $this->createStudent($this->level, $this->year, (string)$i);
        }

        $response = $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams/distribute/preview", [
                '_token'    => csrf_token(),
                'mode'      => 'balanced',
                'team_size' => 4,
            ]);

        $response->assertOk();
        $response->assertSessionMissing('errors');

        // CRITICAL: teams table must still be empty
        $this->assertDatabaseCount('teams', 0);
    }

    // ── I-08: Confirm — creates teams + pivot rows from session ──────────────

    public function test_I08_confirm_creates_teams_in_db(): void
    {
        $students = [];
        for ($i = 0; $i < 4; $i++) {
            $students[] = $this->createStudent($this->level, $this->year, "conf{$i}");
        }

        // Manually seed the session as the preview step would
        $sessionGroups = [
            [$students[0]->id, $students[1]->id],
            [$students[2]->id, $students[3]->id],
        ];

        $this->actingAs($this->doctor, 'web')
            ->withSession([
                'distribution.groups'    => $sessionGroups,
                'distribution.remaining' => [],
                'distribution.level_id'  => $this->level->id,
                'distribution.year_id'   => $this->year->id,
                'distribution.mode'      => 'balanced',
                'distribution.team_size' => 2,
            ])
            ->post("/doctor/{$this->level->id}/teams/distribute/confirm", [
                '_token' => csrf_token(),
            ])
            ->assertRedirect(route('doctor.teams.index', $this->level));

        $this->assertDatabaseCount('teams', 2);

        foreach ($students as $s) {
            $this->assertDatabaseHas('team_student', [
                'student_id'       => $s->id,
                'academic_year_id' => $this->year->id,
            ]);
        }
    }

    // ── I-09: Confirm without session → redirects, no teams created ──────────

    public function test_I09_confirm_without_session_redirects_no_teams(): void
    {
        $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams/distribute/confirm", [
                '_token' => csrf_token(),
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('teams', 0);
    }

    // ── I-10: Double confirm — second POST does not double the teams ──────────

    public function test_I10_double_confirm_does_not_duplicate_teams(): void
    {
        $s1 = $this->createStudent($this->level, $this->year, 'dc1');
        $s2 = $this->createStudent($this->level, $this->year, 'dc2');

        $sessionData = [
            'distribution.groups'    => [[$s1->id, $s2->id]],
            'distribution.remaining' => [],
            'distribution.level_id'  => $this->level->id,
            'distribution.year_id'   => $this->year->id,
            'distribution.mode'      => 'balanced',
            'distribution.team_size' => 2,
        ];

        // First confirm
        $this->actingAs($this->doctor, 'web')
            ->withSession($sessionData)
            ->post("/doctor/{$this->level->id}/teams/distribute/confirm", [
                '_token' => csrf_token(),
            ]);

        $this->assertDatabaseCount('teams', 1);

        // Second confirm — session is now cleared, so it should redirect without creating more
        $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams/distribute/confirm", [
                '_token' => csrf_token(),
            ])
            ->assertRedirect();

        // Still only 1 team
        $this->assertDatabaseCount('teams', 1);
    }

    // ── D-01 / D-02 logic: preview endpoint renders, no DB write ─────────────

    public function test_preview_endpoint_returns_view_and_no_db_write(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->createStudent($this->level, $this->year, "pv{$i}");
        }

        $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams/distribute/preview", [
                '_token'    => csrf_token(),
                'mode'      => 'balanced',
                'team_size' => 3,
            ]);

        // teams table must remain untouched
        $this->assertDatabaseCount('teams', 0);
        $this->assertDatabaseCount('team_student', 0);
    }
}
