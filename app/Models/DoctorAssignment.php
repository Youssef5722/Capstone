<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorAssignment extends Model
{
    protected $fillable = ['doctor_id', 'level_id', 'academic_year_id'];

    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
    public function level() { return $this->belongsTo(Level::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
}
