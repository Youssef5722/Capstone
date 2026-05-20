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

    /** In-memory cache for active(). Class-level so tests can reset it. */
    private static ?self $activeCache = null;

    /**
     * Return the currently active academic year (or null).
     */
    public static function active(): ?self
    {
        return self::$activeCache ??= static::where('is_active', true)->first();
    }

    /**
     * Reset the active() cache. Call this in tests after changing is_active.
     */
    public static function clearActiveCache(): void
    {
        self::$activeCache = null;
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

    // ── Sprint 3 Relationships ─────────────────────────────────────────────────

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
