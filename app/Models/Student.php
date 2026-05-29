<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = ['name','university_id','email','password','avatar',
        'activation_code','is_active','level_id','academic_year_id',
        'activation_code_expires_at'];
    
    protected $hidden = ['password','activation_code'];
    
    protected function casts(): array {
        return [
            'is_active'=>'boolean',
            'activation_code_expires_at'=>'datetime'
        ];
    }

    public function level()        { return $this->belongsTo(Level::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }

    // ── Sprint 3 Relationships ─────────────────────────────────────────────────

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_student')
                    ->withPivot(['academic_year_id']);
    }

    public function leaderOf()
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    public function teamRequests()
    {
        return $this->hasMany(TeamRequest::class, 'requested_by');
    }
}
