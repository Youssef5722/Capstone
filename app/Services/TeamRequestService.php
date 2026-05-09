<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ProjectIdea;
use App\Models\Student;
use App\Models\Team;
use App\Models\TeamRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeamRequestService
{
    /**
     * Create a change request on behalf of the team leader.
     *
     * Guards (per SOP §6.3):
     * 1. Only the team leader may submit a request.
     * 2. At least one of requested_name or project_idea_id must be present.
     * 3. No pending request may already exist for this team.
     * 4. If project_idea_id given: project must belong to the same level + year.
     */
    public function createRequest(Team $team, Student $student, array $data): TeamRequest
    {
        // Guard 1: only leader
        if ($student->id !== $team->leader_id) {
            abort(403, __('cms.teams.only_leader_can_request'));
        }

        // Guard 2: at least one change specified
        if (empty($data['requested_name']) && empty($data['project_idea_id'])) {
            abort(422, __('validation.team_request_empty_change'));
        }

        // Guard 3: no pending request exists
        if ($team->requests()->where('status', 'pending')->exists()) {
            abort(422, __('cms.teams.pending_request_exists'));
        }

        // Guard 4: project idea must belong to same level + year
        if (! empty($data['project_idea_id'])) {
            $idea = ProjectIdea::where('id', $data['project_idea_id'])
                               ->where('level_id', $team->level_id)
                               ->where('academic_year_id', $team->academic_year_id)
                               ->first();

            if (! $idea) {
                abort(422, __('cms.teams.project_wrong_level'));
            }
        }

        return DB::transaction(function () use ($team, $student, $data) {
            return TeamRequest::create([
                'team_id'          => $team->id,
                'requested_name'   => $data['requested_name'] ?? null,
                'project_idea_id'  => $data['project_idea_id'] ?? null,
                'status'           => 'pending',
                'requested_by'     => $student->id,
            ]);
        });
    }

    /**
     * Approve a team request.
     *
     * ⚠️ CRITICAL: Only update team name if requested_name is NOT null.
     * A null requested_name must never overwrite the current team name.
     */
    public function approve(TeamRequest $request, User $doctor): void
    {
        DB::transaction(function () use ($request, $doctor) {
            $team = $request->team;

            // Only update name when the request actually carries one
            if ($request->requested_name !== null) {
                $team->update(['name' => $request->requested_name]);
            }

            // Only upsert team_project when a project idea was requested
            if ($request->project_idea_id !== null) {
                DB::table('team_project')->updateOrInsert(
                    ['team_id' => $team->id],
                    ['project_idea_id' => $request->project_idea_id]
                );
            }

            $request->update([
                'status'      => 'approved',
                'reviewed_by' => $doctor->id,
                'reviewed_at' => now(),
            ]);
        });
    }

    /**
     * Reject a team request — team state is left unchanged.
     */
    public function reject(TeamRequest $request, User $doctor): void
    {
        $request->update([
            'status'      => 'rejected',
            'reviewed_by' => $doctor->id,
            'reviewed_at' => now(),
        ]);
    }
}
