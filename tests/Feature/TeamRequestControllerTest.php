<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\TeamRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\Sprint3TestHelpers;
use Tests\TestCase;

/**
 * B.3 — Team Request Controller Integration Tests (I-11 … I-18)
 *
 * Covers: student submits request, doctor approves/rejects,
 * cross-level access blocked.
 */
class TeamRequestControllerTest extends TestCase
{
    use DatabaseMigrations, Sprint3TestHelpers;

    private AcademicYear       $year;
    private Level              $level2;
    private \App\Models\User   $doctorA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetYearCache();

        $this->level2  = $this->createLevel('Level 2');
        $this->year    = $this->createActiveYear();
        $this->doctorA = $this->createDoctor($this->level2, $this->year, 'doctora@test.com');
    }

    protected function tearDown(): void
    {
        $this->resetYearCache();
        parent::tearDown();
    }

    // ── I-11: Leader submits name-change request → pending row, null project ──

    public function test_I11_leader_submits_name_request_creates_pending_row(): void
    {
        $leader = $this->createStudent($this->level2, $this->year, 'ldr1');
        $team   = $this->createTeam($this->level2, $this->year, $leader);

        $response = $this->actingAs($leader, 'student')
            ->post('/student/team/request', [
                '_token'         => csrf_token(),
                'requested_name' => 'New Team Name',
                'project_idea_id'=> null,
            ]);

        $response->assertRedirect(route('student.team.show'));

        $this->assertDatabaseHas('team_requests', [
            'team_id'        => $team->id,
            'requested_name' => 'New Team Name',
            'project_idea_id'=> null,
            'status'         => 'pending',
            'requested_by'   => $leader->id,
        ]);
    }

    // ── I-12: Non-leader submits request → 403, no row created ───────────────

    public function test_I12_nonleader_submit_returns_403(): void
    {
        $leader  = $this->createStudent($this->level2, $this->year, 'ldr2');
        $member  = $this->createStudent($this->level2, $this->year, 'mbr2');
        $team    = $this->createTeam($this->level2, $this->year, $leader, [$member]);

        $response = $this->actingAs($member, 'student')
            ->post('/student/team/request', [
                '_token'         => csrf_token(),
                'requested_name' => 'Attempt',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('team_requests', 0);
    }

    // ── I-13: Both fields empty → 422 / redirect with error ──────────────────

    public function test_I13_both_empty_fields_returns_validation_error(): void
    {
        $leader = $this->createStudent($this->level2, $this->year, 'ldr3');
        $this->createTeam($this->level2, $this->year, $leader);

        $response = $this->actingAs($leader, 'student')
            ->post('/student/team/request', [
                '_token'          => csrf_token(),
                'requested_name'  => null,
                'project_idea_id' => null,
            ]);

        // Service or form request validation will either abort(422) or redirect back
        $this->assertTrue(
            in_array($response->status(), [302, 303, 422]),
            "I-13: Expected redirect or 422 but got {$response->status()}"
        );

        $this->assertDatabaseCount('team_requests', 0);
    }

    // ── I-14: Doctor approves name-only request → team name updated ───────────

    public function test_I14_approve_name_only_updates_team_name(): void
    {
        $leader  = $this->createStudent($this->level2, $this->year, 'ldr4');
        $team    = $this->createTeam($this->level2, $this->year, $leader);
        $request = $this->createPendingRequest($team, $leader, 'Brand New Name', null);

        $this->actingAs($this->doctorA, 'web')
            ->post("/doctor/{$this->level2->id}/requests/{$request->id}/approve", [
                '_token' => csrf_token(),
            ])
            ->assertRedirect(route('doctor.requests.index', $this->level2));

        // Team name updated
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Brand New Name']);

        // team_project NOT touched
        $this->assertDatabaseMissing('team_project', ['team_id' => $team->id]);

        // Request marked approved with reviewer info
        $this->assertDatabaseHas('team_requests', [
            'id'          => $request->id,
            'status'      => 'approved',
            'reviewed_by' => $this->doctorA->id,
        ]);
        $updated = TeamRequest::find($request->id);
        $this->assertNotNull($updated->reviewed_at);
    }

    // ── I-15: Doctor approves project-only request → team_project created ─────

    public function test_I15_approve_project_only_creates_team_project_row(): void
    {
        $leader  = $this->createStudent($this->level2, $this->year, 'ldr5');
        $team    = $this->createTeam($this->level2, $this->year, $leader);
        $idea    = $this->createProjectIdea($this->doctorA, $this->level2, $this->year);
        $request = $this->createPendingRequest($team, $leader, null, $idea->id);

        $originalName = $team->name;

        $this->actingAs($this->doctorA, 'web')
            ->post("/doctor/{$this->level2->id}/requests/{$request->id}/approve", [
                '_token' => csrf_token(),
            ]);

        // team_project row created
        $this->assertDatabaseHas('team_project', [
            'team_id'         => $team->id,
            'project_idea_id' => $idea->id,
        ]);

        // Team name must NOT change
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => $originalName]);
    }

    // ── I-16: Both fields present → team name + team_project both updated ─────

    public function test_I16_approve_both_fields_updates_name_and_project(): void
    {
        $leader  = $this->createStudent($this->level2, $this->year, 'ldr6');
        $team    = $this->createTeam($this->level2, $this->year, $leader);
        $idea    = $this->createProjectIdea($this->doctorA, $this->level2, $this->year);
        $request = $this->createPendingRequest($team, $leader, 'Combined Name', $idea->id);

        $this->actingAs($this->doctorA, 'web')
            ->post("/doctor/{$this->level2->id}/requests/{$request->id}/approve", [
                '_token' => csrf_token(),
            ]);

        $this->assertDatabaseHas('teams',       ['id' => $team->id, 'name' => 'Combined Name']);
        $this->assertDatabaseHas('team_project', ['team_id' => $team->id, 'project_idea_id' => $idea->id]);
    }

    // ── I-17: Doctor rejects request → status=rejected, team unchanged ────────

    public function test_I17_reject_sets_rejected_status_team_unchanged(): void
    {
        $leader      = $this->createStudent($this->level2, $this->year, 'ldr7');
        $team        = $this->createTeam($this->level2, $this->year, $leader);
        $originalName = $team->name;
        $request      = $this->createPendingRequest($team, $leader, 'Rejected Name', null);

        $this->actingAs($this->doctorA, 'web')
            ->post("/doctor/{$this->level2->id}/requests/{$request->id}/reject", [
                '_token' => csrf_token(),
            ]);

        $this->assertDatabaseHas('team_requests', [
            'id'          => $request->id,
            'status'      => 'rejected',
            'reviewed_by' => $this->doctorA->id,
        ]);

        // Team name must be unchanged
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => $originalName]);
    }

    // ── I-18: Doctor B tries to approve Level 2 request → 403 ────────────────

    public function test_I18_wrong_level_doctor_approve_returns_403(): void
    {
        $level4  = $this->createLevel('Level 4');
        $doctorB = $this->createDoctor($level4, $this->year, 'doctorb@test.com');

        $leader  = $this->createStudent($this->level2, $this->year, 'ldr8');
        $team    = $this->createTeam($this->level2, $this->year, $leader);
        $request = $this->createPendingRequest($team, $leader, 'Cross-Level Attempt');

        $this->actingAs($doctorB, 'web')
            ->post("/doctor/{$this->level2->id}/requests/{$request->id}/approve", [
                '_token' => csrf_token(),
            ])
            ->assertStatus(403);

        // Status must remain pending
        $this->assertDatabaseHas('team_requests', ['id' => $request->id, 'status' => 'pending']);
    }
}
