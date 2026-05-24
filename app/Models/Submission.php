<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Submission extends Model
{
    protected $fillable = [
        'submittable_id',
        'submittable_type',
        'submitted_by',
        'file_path',
        'file_name',
        'file_type',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    /**
     * The polymorphic parent (Task or SubTask).
     */
    public function submittable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The student who submitted the file.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'submitted_by');
    }
}
