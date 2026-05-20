<?php

namespace Tests;

use App\Models\AcademicYear;
use App\Models\DoctorAssignment;
use App\Models\Level;
use App\Models\ProjectIdea;
use App\Models\Role;
use App\Models\Student;
use App\Models\Team;
use App\Models\TeamRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Shared scaffold helpers for Sprint 3 Feature tests.
 *
 * Mix this into any Feature test class alongside RefreshDatabase.
 * Provides factory-style helpers that create the minimum data
 * needed to exercise controllers through middleware.
 */
trait Sprint3TestHelpers
{
    // ── Static-cache buster ───────────────────────────────────────────────────

    /**
     * Reset the AcademicYear::active() cache.
     * Now delegates to the model's own clearActiveCache() method
     * which resets a class-level static property (PHP 8.5-safe).
     */
    protected function resetYearCache(): void
    {
        \App\Models\AcademicYear::clearActiveCache();
    }



    // ── Base data creators ────────────────────────────────────────────────────

    protected function createRoles(): array
    {
        $admin  = Role::firstOrCreate(['name' => 'admin']);
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        return [$admin, $doctor];
    }

    protected function createActiveYear(string $name = '2025-2026'): AcademicYear
    {
        // Deactivate any existing active year first
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $year = AcademicYear::create([
            'name'       => $name,
            'start_date' => now()->startOfYear(),
            'end_date'   => now()->endOfYear(),
            'is_active'  => true,
        ]);
        $this->resetYearCache();
        return $year;
    }

    protected function createLevel(string $name = 'Level 2'): Level
    {
        return Level::firstOrCreate(['name' => $name]);
    }

    protected function createAdmin(): User
    {
        [$adminRole] = $this->createRoles();
        return User::create([
            'name'        => 'Admin User',
            'email'       => 'admin@test.com',
            'national_id' => 'ADMIN001',
            'password'    => Hash::make('password'),
            'role_id'     => $adminRole->id,
            'status'      => 'approved',
        ]);
    }

    protected function createDoctor(Level $level, AcademicYear $year, string $email = 'doctor@test.com'): User
    {
        [, $doctorRole] = $this->createRoles();
        // national_id must be unique — derive from email for determinism
        $natId = 'DOC' . strtoupper(substr(md5($email), 0, 9));
        $doctor = User::firstOrCreate(['email' => $email], [
            'name'        => 'Doctor User',
            'national_id' => $natId,
            'password'    => Hash::make('password'),
            'role_id'     => $doctorRole->id,
            'status'      => 'approved',
        ]);

        DoctorAssignment::firstOrCreate([
            'doctor_id'        => $doctor->id,
            'level_id'         => $level->id,
            'academic_year_id' => $year->id,
        ]);

        return $doctor;
    }

    protected function createStudent(Level $level, AcademicYear $year, string $suffix = '1'): Student
    {
        return Student::create([
            'name'          => "Student {$suffix}",
            'university_id' => "U{$suffix}" . rand(1000, 9999),
            'email'         => "student{$suffix}_" . rand(1000,9999) . "@test.com",
            'password'      => Hash::make('password'),
            'is_active'     => true,
            'level_id'      => $level->id,
            'academic_year_id' => $year->id,
        ]);
    }

    protected function createTeam(Level $level, AcademicYear $year, Student $leader, array $members = []): Team
    {
        $team = Team::create([
            'name'             => 'Test Team ' . rand(100, 999),
            'leader_id'        => $leader->id,
            'level_id'         => $level->id,
            'academic_year_id' => $year->id,
        ]);

        $allMembers = array_merge([$leader->id], array_map(fn($s) => $s->id, $members));
        foreach ($allMembers as $sid) {
            $team->students()->attach($sid, ['academic_year_id' => $year->id]);
        }

        return $team;
    }

    protected function createPendingRequest(Team $team, Student $leader, ?string $name = null, ?int $ideaId = null): TeamRequest
    {
        return TeamRequest::create([
            'team_id'         => $team->id,
            'requested_name'  => $name,
            'project_idea_id' => $ideaId,
            'status'          => 'pending',
            'requested_by'    => $leader->id,
        ]);
    }

    protected function createProjectIdea(User $doctor, Level $level, AcademicYear $year): ProjectIdea
    {
        return ProjectIdea::create([
            'doctor_id'        => $doctor->id,
            'level_id'         => $level->id,
            'academic_year_id' => $year->id,
            'title'            => 'Test Idea ' . rand(100, 999),
            'description'      => 'A test project idea.',
        ]);
    }
}
