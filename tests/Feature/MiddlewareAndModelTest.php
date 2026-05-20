<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Sprint3TestHelpers;
use Tests\TestCase;

/**
 * B.5 — Middleware & Scoping Tests (I-23 … I-27)
 * B.6 — Model Relationship Tests (I-28 … I-33)
 */
class MiddlewareAndModelTest extends TestCase
{
    use DatabaseMigrations, Sprint3TestHelpers;

    private AcademicYear     $year;
    private Level            $level2;
    private \App\Models\User $doctorA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetYearCache();

        $this->level2  = $this->createLevel('Level 2');
        $this->year    = $this->createActiveYear();
        $this->doctorA = $this->createDoctor($this->level2, $this->year, 'mw_doc@test.com');
    }

    protected function tearDown(): void
    {
        $this->resetYearCache();
        parent::tearDown();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // B.5 — Middleware
    // ═══════════════════════════════════════════════════════════════════════════

    // I-23: Doctor assigned to level → request passes (200 OK)
    public function test_I23_doctor_assigned_to_level_gets_200(): void
    {
        $this->actingAs($this->doctorA, 'web')
            ->get("/doctor/{$this->level2->id}/teams")
            ->assertOk();
    }

    // I-24: Doctor NOT assigned to level → 403
    public function test_I24_doctor_not_assigned_to_level_gets_403(): void
    {
        $level4 = $this->createLevel('Level 4');

        $this->actingAs($this->doctorA, 'web')
            ->get("/doctor/{$level4->id}/teams")
            ->assertStatus(403);
    }

    // I-25: No active academic year → doctor routes blocked
    public function test_I25_no_active_year_blocks_doctor_routes(): void
    {
        $this->year->update(['is_active' => false]);
        $this->resetYearCache();

        $response = $this->actingAs($this->doctorA, 'web')
            ->get("/doctor/{$this->level2->id}/teams");

        // EnsureAcademicYearActive or EnsureDoctorLevelAccess will block (403 or redirect)
        $this->assertNotEquals(200, $response->status(),
            'I-25: Doctor routes must be blocked when no year is active');
    }

    // I-26: Admin cannot access doctor-only routes (role:doctor middleware)
    public function test_I26_admin_cannot_access_doctor_routes(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'web')
            ->get("/doctor/{$this->level2->id}/teams")
            ->assertStatus(403);
    }

    // I-27: Data scoping — teams from Year A never appear in Year A query of different year
    public function test_I27_teams_scoped_to_correct_academic_year(): void
    {
        $s1 = $this->createStudent($this->level2, $this->year, 'scope1');

        // Year A team
        $teamA = $this->createTeam($this->level2, $this->year, $s1);

        // Create Year B (inactive) + team
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $yearB = AcademicYear::create([
            'name'       => '2024-2025',
            'start_date' => now()->subYear()->startOfYear(),
            'end_date'   => now()->subYear()->endOfYear(),
            'is_active'  => false,
        ]);
        $s2    = $this->createStudent($this->level2, $yearB, 'scope2');
        $teamB = Team::create([
            'name'             => 'Year B Team',
            'leader_id'        => $s2->id,
            'level_id'         => $this->level2->id,
            'academic_year_id' => $yearB->id,
        ]);

        // Query only Year A teams
        $yearATeams = Team::where('level_id', $this->level2->id)
                          ->where('academic_year_id', $this->year->id)
                          ->get();

        $ids = $yearATeams->pluck('id')->toArray();
        $this->assertContains($teamA->id, $ids,   'I-27: Year A team must appear');
        $this->assertNotContains($teamB->id, $ids, 'I-27: Year B team must not appear');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // B.6 — Model Relationships
    // ═══════════════════════════════════════════════════════════════════════════

    // I-28: Team::students() — returns collection with pivot academic_year_id
    public function test_I28_team_members_relation_returns_correct_students(): void
    {
        $s1 = $this->createStudent($this->level2, $this->year, 'rel1');
        $s2 = $this->createStudent($this->level2, $this->year, 'rel2');
        $s3 = $this->createStudent($this->level2, $this->year, 'rel3');

        $team = $this->createTeam($this->level2, $this->year, $s1, [$s2, $s3]);

        $members = $team->students()->get();

        $this->assertCount(3, $members, 'I-28: 3 students in team');
        foreach ($members as $m) {
            $this->assertEquals($this->year->id, $m->pivot->academic_year_id,
                'I-28: pivot academic_year_id must match');
        }
    }

    // I-29: Team::leader() returns the correct single Student
    public function test_I29_team_leader_relation_returns_correct_student(): void
    {
        $leader = $this->createStudent($this->level2, $this->year, 'ldr_rel');
        $team   = $this->createTeam($this->level2, $this->year, $leader);

        $this->assertEquals($leader->id, $team->leader->id, 'I-29: leader() must return correct student');
    }

    // I-30: Student::teams() — student in 2 teams across 2 years returns 2 teams
    public function test_I30_student_teams_relation_across_years(): void
    {
        $s = $this->createStudent($this->level2, $this->year, 'multi');

        // Team in year 1 (active)
        $t1 = $this->createTeam($this->level2, $this->year, $s);

        // Create year 2
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $this->resetYearCache();
        $y2 = AcademicYear::create([
            'name'       => '2023-2024',
            'start_date' => now()->subYears(2)->startOfYear(),
            'end_date'   => now()->subYears(2)->endOfYear(),
            'is_active'  => false,
        ]);

        $t2 = Team::create([
            'name'             => null,
            'leader_id'        => $s->id,
            'level_id'         => $this->level2->id,
            'academic_year_id' => $y2->id,
        ]);
        $t2->students()->attach($s->id, ['academic_year_id' => $y2->id]);

        $teams = $s->teams()->get();
        $this->assertCount(2, $teams, 'I-30: Student in 2 teams across 2 years');
    }

    // I-31: TeamRequest->projectIdea returns null when project_idea_id is null
    public function test_I31_team_request_null_project_idea_returns_null(): void
    {
        $leader  = $this->createStudent($this->level2, $this->year, 'null_proj');
        $team    = $this->createTeam($this->level2, $this->year, $leader);
        $request = $this->createPendingRequest($team, $leader, 'Name only', null);

        $this->assertNull($request->projectIdea, 'I-31: projectIdea must be null when not set');
    }

    // I-32: AcademicYear::teams() — active year has correct count
    public function test_I32_academic_year_teams_relation(): void
    {
        $students = [];
        for ($i = 0; $i < 5; $i++) {
            $students[] = $this->createStudent($this->level2, $this->year, "yr_rel{$i}");
        }

        foreach ($students as $s) {
            $this->createTeam($this->level2, $this->year, $s);
        }

        $this->assertCount(5, $this->year->teams, 'I-32: Academic year must have 5 teams');
    }

    // I-33: Level::teams() — returns only teams for that level
    public function test_I33_level_teams_relation_scoped_to_level(): void
    {
        $level4 = $this->createLevel('Level 4');
        $s1     = $this->createStudent($this->level2, $this->year, 'lvl_rel1');
        $s2     = $this->createStudent($this->level2, $this->year, 'lvl_rel2');
        $s3     = $this->createStudent($this->level2, $this->year, 'lvl_rel3');

        $this->createTeam($this->level2, $this->year, $s1);
        $this->createTeam($this->level2, $this->year, $s2);
        $this->createTeam($this->level2, $this->year, $s3);

        // Create one team in level4 to confirm it doesn't bleed
        $s4 = $this->createStudent($level4, $this->year, 'lvl_rel4');
        Team::create(['name'=>null,'leader_id'=>$s4->id,'level_id'=>$level4->id,'academic_year_id'=>$this->year->id]);

        $level2Teams = $this->level2->teams()->get();
        $this->assertCount(3, $level2Teams, 'I-33: Level 2 must have exactly 3 teams');

        foreach ($level2Teams as $t) {
            $this->assertEquals($this->level2->id, $t->level_id, 'I-33: All teams must belong to Level 2');
        }
    }
}
