<?php

namespace App\Services;

use App\Models\ProjectIdea;

class ProjectIdeaService
{
    /**
     * Return ideas scoped by doctor + level + academic year, newest first.
     */
    public function getIdeas(int $doctorId, int $levelId, int $yearId)
    {
        return ProjectIdea::where('doctor_id', $doctorId)
                          ->where('level_id', $levelId)
                          ->where('academic_year_id', $yearId)
                          ->latest()
                          ->get();
    }

    /**
     * Store a new project idea.
     */
    public function store(array $data): ProjectIdea
    {
        return ProjectIdea::create($data);
    }

    /**
     * Update an existing project idea.
     */
    public function update(ProjectIdea $idea, array $data): ProjectIdea
    {
        $idea->update($data);
        return $idea->fresh();
    }

    /**
     * Delete a project idea (hard delete — no soft-delete on ideas in Sprint 2).
     */
    public function delete(ProjectIdea $idea): void
    {
        $idea->delete();
    }
}
