<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    protected $fillable = ['name', 'leader_id', 'level_id', 'academic_year_id'];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'leader_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(TeamRequest::class);
    }

    /**
     * Members of the team via team_student pivot.
     * withPivot(['academic_year_id']) exposes the pivot column for scoped queries.
     * No timestamps on the pivot table (per SOP §5.3).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'team_student')
                    ->withPivot(['academic_year_id']);
    }

    /**
     * Project ideas assigned to this team via the team_project pivot.
     * This replaces the broken join-based HasOne approach and supports eager loading.
     * Use the `currentProject` accessor to get the single assigned project idea.
     */
    public function projectIdeas(): BelongsToMany
    {
        return $this->belongsToMany(ProjectIdea::class, 'team_project', 'team_id', 'project_idea_id');
    }

    /**
     * Accessor: $team->currentProject
     *
     * Returns the first (and only) assigned project idea for this team,
     * or null if none is assigned. Uses the already-loaded 'projectIdeas'
     * relation when available (avoids N+1 when eager-loaded via `with('projectIdeas')`).
     *
     * All existing views that use $team->currentProject continue to work unchanged.
     */
    public function getCurrentProjectAttribute(): ?ProjectIdea
    {
        if ($this->relationLoaded('projectIdeas')) {
            return $this->projectIdeas->first();
        }

        return $this->projectIdeas()->first();
    }

    // ── Sprint 4 Relationships ─────────────────────────────────────────────────

    public function workspace(): HasOne
    {
        return $this->hasOne(Workspace::class);
    }
}
