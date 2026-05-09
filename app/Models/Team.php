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
     * The project idea assigned to this team via the team_project pivot.
     * Returns a HasOne through a raw join since team_project has no Eloquent model.
     */
    public function projectIdea(): HasOne
    {
        return $this->hasOne(ProjectIdea::class, 'id', 'id')
                    ->join('team_project', 'project_ideas.id', '=', 'team_project.project_idea_id')
                    ->where('team_project.team_id', $this->id);
    }
}
