<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Workspace;

class WorkspaceService
{
    /**
     * Create a workspace for the given team.
     * Uses firstOrCreate to be idempotent — calling this twice will not create duplicates.
     *
     * Called from TeamRequestService@approve when a project_idea is part of the request.
     */
    public function createForTeam(Team $team): Workspace
    {
        return Workspace::firstOrCreate(
            ['team_id' => $team->id],
            [
                'academic_year_id' => $team->academic_year_id,
                'level_id'         => $team->level_id,
                'status'           => 'active',
            ]
        );
    }
}
