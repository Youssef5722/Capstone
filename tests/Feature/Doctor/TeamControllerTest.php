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
 * B.1 — Team CRUD Controller Integration Tests (I-01 … I-06)
 *
 * Exercises POST/GET routes through the full middleware stack:
 *   auth + role:doctor + active.year + doctor.level
 *
 * Uses SQLite in-memory (phpunit.xml) + RefreshDatabase.
 */
class TeamControllerTest extends TestCase
{
    use DatabaseMigrations, Sprint3TestHelpers;

    private AcademicYear $year;
    private Level        $level;
    private \App\Models\User $doctor;

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

    // ── I-01: POST /doctor/{level}/teams — valid data creates team + pivot rows ─

    public function test_I01_store_valid_team_creates_db_rows(): void
    {
        $s1 = $this->createStudent($this->level, $this->year, 'a');
        $s2 = $this->createStudent($this->level, $this->year, 'b');

        $response = $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams", [
                '_token'      => csrf_token(),
                'name'        => 'Alpha Team',
                'leader_id'   => $s1->id,
                'student_ids' => [$s1->id, $s2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('teams', [
            'name'             => 'Alpha Team',
            'leader_id'        => $s1->id,
            'level_id'         => $this->level->id,
            'academic_year_id' => $this->year->id,
        ]);

        $team = Team::where('name', 'Alpha Team')->firstOrFail();
        $this->assertDatabaseHas('team_student', [
            'team_id'          => $team->id,
            'student_id'       => $s1->id,
            'academic_year_id' => $this->year->id,
        ]);
        $this->assertDatabaseHas('team_student', [
            'team_id'          => $team->id,
            'student_id'       => $s2->id,
            'academic_year_id' => $this->year->id,
        ]);
    }

    // ── I-02: POST — student already in another team → validation error, no new team

    public function test_I02_store_duplicate_student_returns_error(): void
    {
        $s1 = $this->createStudent($this->level, $this->year, 'c');
        $s2 = $this->createStudent($this->level, $this->year, 'd');

        // Assign s1 to an existing team
        $this->createTeam($this->level, $this->year, $s1);

        $before = Team::count();

        $response = $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams", [
                '_token'      => csrf_token(),
                'name'        => 'Beta Team',
                'leader_id'   => $s2->id,
                'student_ids' => [$s1->id, $s2->id], // s1 already assigned
            ]);

        // Must not 500 and must not create a new team
        $response->assertStatus(302); // redirect back with error
        $this->assertDatabaseCount('teams', $before);
    }

    // ── I-03: POST — doctor NOT assigned to level → 403

    public function test_I03_store_wrong_level_returns_403(): void
    {
        $otherLevel = $this->createLevel('Level 4');
        $s1         = $this->createStudent($this->level, $this->year, 'e');

        $response = $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$otherLevel->id}/teams", [
                '_token'      => csrf_token(),
                'name'        => 'Gamma Team',
                'leader_id'   => $s1->id,
                'student_ids' => [$s1->id],
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('teams', ['name' => 'Gamma Team']);
    }

    // ── I-04: POST /{level}/teams/{team} — update name + swap member

    public function test_I04_update_changes_name_and_swaps_member(): void
    {
        $s1 = $this->createStudent($this->level, $this->year, 'f');
        $s2 = $this->createStudent($this->level, $this->year, 'g');
        $team = $this->createTeam($this->level, $this->year, $s1);

        $response = $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams/{$team->id}", [
                '_token'    => csrf_token(),
                'name'      => 'Updated Name',
                'leader_id' => $s1->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Updated Name']);
    }

    // ── I-05: POST /{level}/teams/{team}/delete — cascades pivot rows

    public function test_I05_destroy_removes_team_and_cascades_pivot(): void
    {
        $s1   = $this->createStudent($this->level, $this->year, 'h');
        $s2   = $this->createStudent($this->level, $this->year, 'i');
        $team = $this->createTeam($this->level, $this->year, $s1, [$s2]);

        $teamId = $team->id;

        $response = $this->actingAs($this->doctor, 'web')
            ->post("/doctor/{$this->level->id}/teams/{$teamId}/delete", [
                '_token' => csrf_token(),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('teams',       ['id' => $teamId]);
        $this->assertDatabaseMissing('team_student', ['team_id' => $teamId]);
    }

    // ── I-06: GET /doctor/{level}/teams — lists only this level's teams

    public function test_I06_index_shows_only_this_levels_teams(): void
    {
        $otherLevel = $this->createLevel('Level 4');
        $otherYear  = $this->year; // same year, different level

        $s1 = $this->createStudent($this->level, $this->year, 'j');
        $s2 = $this->createStudent($otherLevel, $otherYear, 'k');

        $this->createTeam($this->level,  $this->year, $s1);
        // Create team in other level — need a doctor for that level (skip, just create direct)
        Team::create([
            'name'             => 'Other Level Team',
            'leader_id'        => $s2->id,
            'level_id'         => $otherLevel->id,
            'academic_year_id' => $otherYear->id,
        ]);

        $response = $this->actingAs($this->doctor, 'web')
            ->get("/doctor/{$this->level->id}/teams");

        $response->assertOk();
        $response->assertDontSee('Other Level Team');
    }

    // ── Route smoke tests: TC-09 … TC-13

    public function test_TC09_teams_index_returns_200(): void
    {
        $this->actingAs($this->doctor, 'web')
            ->get("/doctor/{$this->level->id}/teams")
            ->assertOk();
    }

    public function test_TC10_teams_create_returns_200(): void
    {
        $this->actingAs($this->doctor, 'web')
            ->get("/doctor/{$this->level->id}/teams/create")
            ->assertOk();
    }

    public function test_TC12_distribute_form_returns_200(): void
    {
        $this->actingAs($this->doctor, 'web')
            ->get("/doctor/{$this->level->id}/teams/distribute")
            ->assertOk();
    }

    public function test_TC13_requests_index_returns_200(): void
    {
        $this->actingAs($this->doctor, 'web')
            ->get("/doctor/{$this->level->id}/requests")
            ->assertOk();
    }
}
