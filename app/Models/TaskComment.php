<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaskComment extends Model
{
    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'commented_by_id',
        'commented_by_type',
        'comment',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    /**
     * The polymorphic target: Task or SubTask.
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The polymorphic author: User (doctor) or Student.
     * Laravel's morphTo requires specifying the custom column names.
     */
    public function commentedBy(): MorphTo
    {
        return $this->morphTo('commentedBy', 'commented_by_type', 'commented_by_id');
    }
}
