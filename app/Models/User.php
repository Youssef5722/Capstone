<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'national_id', 'phone', 'role_id', 'status', 'requested_levels'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'requested_levels' => 'array',
        ];
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function doctorAssignments()
    {
        return $this->hasMany(DoctorAssignment::class, 'doctor_id');
    }

    // ── Sprint 3 Relationships ─────────────────────────────────────────────────

    public function reviewedRequests()
    {
        return $this->hasMany(TeamRequest::class, 'reviewed_by');
    }
}
