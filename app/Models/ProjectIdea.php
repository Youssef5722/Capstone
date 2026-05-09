<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectIdea extends Model
{
    protected $fillable = [
        'doctor_id',
        'level_id',
        'academic_year_id',
        'title',
        'description',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ── Sprint 3 Relationships ─────────────────────────────────────────────────

    public function teamRequests(): HasMany
    {
        return $this->hasMany(TeamRequest::class);
    }
}
