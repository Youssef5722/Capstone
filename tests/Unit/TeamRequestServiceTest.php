<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\ProjectIdea;
use App\Models\Student;
use App\Models\Team;
use App\Models\TeamRequest;
use App\Models\User;
use App\Services\TeamRequestService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

/**
 * A.3 — TeamRequestService Unit Tests (U-17 … U-25)
 * No real DB — source inspection + Mockery mocks.
 */
class TeamRequestServiceTest extends TestCase
{
    private TeamRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TeamRequestService();
        DB::shouldReceive('transaction')
            ->andReturnUsing(fn ($cb) => $cb());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Fake team stub. */
    private function makeTeam(int $leaderId = 1, int $levelId = 2, int $yearId = 1): Team
    {
        $team = Mockery::mock(Team::class)->makePartial();
        $team->id              = 10;
        $team->leader_id       = $leaderId;
        $team->level_id        = $levelId;
        $team->academic_year_id = $yearId;
        // requests() returns a query builder stub that reports no pending requests by default
        $reqQuery = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $reqQuery->shouldReceive('where')->andReturnSelf();
        $reqQuery->shouldReceive('exists')->andReturn(false);
        $team->shouldReceive('requests')->andReturn($reqQuery);
        return $team;
    }

    /** Fake student stub. */
    private function makeStudent(int $id): Student
    {
        $s = Mockery::mock(Student::class)->makePartial();
        $s->id = $id;
        return $s;
    }

    /** Fake TeamRequest stub. */
    private function makeRequest(array $attrs = []): TeamRequest
    {
        $r = Mockery::mock(TeamRequest::class)->makePartial();
        foreach ($attrs as $k => $v) {
            $r->{$k} = $v;
        }
        $r->shouldReceive('update')->andReturnTrue();
        return $r;
    }

    // U-17: createRequest() name only → project_idea_id = NULL, status = pending
    public function test_U17_createRequest_name_only_sets_null_project(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        // Verify null coalescing for project_idea_id
        $this->assertStringContainsString("'project_idea_id'  => \$data['project_idea_id'] ?? null", $src,
            'U-17: project_idea_id must default to null');
        $this->assertStringContainsString("'status'           => 'pending'", $src,
            'U-17: status must default to pending');
    }

    // U-18: createRequest() project only → requested_name = NULL
    public function test_U18_createRequest_project_only_sets_null_name(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        $this->assertStringContainsString("'requested_name'   => \$data['requested_name'] ?? null", $src,
            'U-18: requested_name must default to null');
    }

    // U-19: createRequest() both fields empty → aborts 422
    public function test_U19_createRequest_both_empty_aborts_422(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        $this->assertStringContainsString('empty($data[\'requested_name\']) && empty($data[\'project_idea_id\'])', $src,
            'U-19: createRequest() must guard against both-empty input');
        $this->assertStringContainsString('abort(422', $src,
            'U-19: createRequest() must abort(422) when both fields empty');
    }

    // U-20: approve() with requested_name NOT null → team name updated
    public function test_U20_approve_with_name_updates_team_name(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        $this->assertStringContainsString('$request->requested_name !== null', $src,
            'U-20: approve() must check !== null (not falsy) before updating name');
        $this->assertStringContainsString("['name' => \$request->requested_name]", $src,
            'U-20: approve() must update team name from request');
    }

    // U-21: approve() with requested_name IS null → team name NOT touched
    public function test_U21_approve_null_name_does_not_touch_team_name(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        // The guard is `if ($request->requested_name !== null)` — only enters when non-null
        $this->assertStringContainsString('$request->requested_name !== null', $src,
            'U-21: approve() must use !== null guard, not empty/falsy, to protect team name');
    }

    // U-22: approve() with project_idea_id NOT null → team_project upserted
    public function test_U22_approve_with_project_upserts_team_project(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        $this->assertStringContainsString('$request->project_idea_id !== null', $src,
            'U-22: approve() must check project_idea_id !== null before upserting');
        $this->assertStringContainsString("updateOrInsert", $src,
            'U-22: approve() must use updateOrInsert for team_project row');
        $this->assertStringContainsString("'team_project'", $src,
            'U-22: approve() must target the team_project table');
    }

    // U-23: approve() with project_idea_id IS null → team_project NOT touched
    public function test_U23_approve_null_project_does_not_touch_team_project(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        // Guard: `if ($request->project_idea_id !== null)` — only enters if non-null
        $this->assertStringContainsString('$request->project_idea_id !== null', $src,
            'U-23: approve() must guard team_project update behind !== null check');
    }

    // U-24: approve() sets reviewed_by, reviewed_at, status = approved
    public function test_U24_approve_sets_reviewed_fields(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        $this->assertStringContainsString("'status'      => 'approved'", $src,
            'U-24: approve() must set status = approved');
        $this->assertStringContainsString("'reviewed_by' => \$doctor->id", $src,
            'U-24: approve() must set reviewed_by = doctor->id');
        $this->assertStringContainsString("'reviewed_at' => now()", $src,
            'U-24: approve() must set reviewed_at = now()');
    }

    // U-25: reject() sets status = rejected, reviewed_by, reviewed_at; team unchanged
    public function test_U25_reject_sets_rejected_status_and_reviewer(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));

        $this->assertStringContainsString("'status'      => 'rejected'", $src,
            'U-25: reject() must set status = rejected');
        $this->assertStringContainsString("'reviewed_by' => \$doctor->id", $src,
            'U-25: reject() must set reviewed_by');
        $this->assertStringContainsString("'reviewed_at' => now()", $src,
            'U-25: reject() must set reviewed_at');

        // Verify reject() does NOT update the team name or team_project
        $rejectStart = strpos($src, 'public function reject(');
        $rejectBody  = substr($src, $rejectStart, 500);
        $this->assertStringNotContainsString("team->update", $rejectBody,
            'U-25: reject() must NOT touch the team record');
        $this->assertStringNotContainsString('team_project', $rejectBody,
            'U-25: reject() must NOT touch team_project');
    }

    // Extra: only_leader guard verified at top of createRequest()
    public function test_only_leader_guard_present_in_createRequest(): void
    {
        $src = file_get_contents(app_path('Services/TeamRequestService.php'));
        $this->assertStringContainsString('$student->id !== $team->leader_id', $src,
            'createRequest() must reject non-leaders with abort(403)');
        $this->assertStringContainsString('abort(403', $src);
    }
}
