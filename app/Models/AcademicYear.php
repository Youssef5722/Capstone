<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    protected function casts(): array {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    // ── Static helper ─────────────────────────────────────────
    /**
     * Return the currently active academic year (or null).
     */
    public static function active(): ?self
    {
        static $cache = null;

        return $cache ??= static::where('is_active', true)->first();
    }

    // ── Relationships ──────────────────────────────────────────
    public function doctorAssignments(): HasMany
    {
        return $this->hasMany(DoctorAssignment::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function projectIdeas(): HasMany
    {
        return $this->hasMany(ProjectIdea::class);
    }
}
