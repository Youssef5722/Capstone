<?php

namespace App\Services;

use App\Models\Phase;
use App\Models\Workspace;

class PhaseService
{
    public function store(Workspace $workspace, array $data): Phase
    {
        return Phase::create(array_merge($data, [
            'workspace_id' => $workspace->id,
        ]));
    }

    public function update(Phase $phase, array $data): Phase
    {
        $phase->update($data);
        return $phase->fresh();
    }

    public function delete(Phase $phase): void
    {
        $phase->delete();
    }
}
