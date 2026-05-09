<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\{AuthController, AdminDoctorController, StudentAuthController};
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\DoctorAssignmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;

// ─── Language Switch ─────────────────────────────────────────────────────────
Route::post('/language/switch', function (Request $request) {
    $locale    = $request->input('locale');
    $available = config('app.available_locales', ['ar', 'en']);
    if (in_array($locale, $available)) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch')->middleware('web');

// ─── Welcome ─────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ─── Guest-only (Admin + Doctor guard) ───────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register',  [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// ─── Authenticated (Admin + Doctor guard) ────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Admin-only ────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

        // Doctor management (no active.year required — admin must manage even without one)
        Route::get('/doctors',                    [AdminDoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/pending',            [AdminDoctorController::class, 'pending'])->name('doctors.pending');
        Route::get('/doctors/rejected',           [AdminDoctorController::class, 'rejected'])->name('doctors.rejected');
        Route::post('/doctors/{doctor}/restore',  [AdminDoctorController::class, 'restore'])->name('doctors.restore');
        Route::post('/doctors/{id}/approve',      [AdminDoctorController::class, 'approve'])->name('doctors.approve');
        Route::post('/doctors/{id}/reject',       [AdminDoctorController::class, 'reject'])->name('doctors.reject');

        // Academic year management — EXCLUDED from active.year middleware intentionally
        Route::get('/academic-years',                [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::get('/academic-years/create',         [AcademicYearController::class, 'create'])->name('academic-years.create');
        Route::post('/academic-years',               [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::get('/academic-years/{academicYear}/edit',      [AcademicYearController::class, 'edit'])->name('academic-years.edit');
        Route::put('/academic-years/{academicYear}',           [AcademicYearController::class, 'update'])->name('academic-years.update');
        Route::post('/academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');
        Route::delete('/academic-years/{academicYear}',        [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');

        // Doctor assignments — requires an active academic year
        Route::middleware('active.year')->group(function () {
            Route::get('/doctors/{id}/assign',      [DoctorAssignmentController::class, 'showAssignForm'])->name('doctors.assign.form');
            Route::post('/doctors/{id}/assign',     [DoctorAssignmentController::class, 'assign'])->name('doctors.assign');
            Route::get('/doctors/{id}/assignments', [DoctorAssignmentController::class, 'show'])->name('doctors.assignments.show');
        });
    });

    // ── Doctor-only ───────────────────────────────────────────────────────────
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    });
});

// ─── Student guard (separate guard) ──────────────────────────────────────────
// Activate + Login are EXCLUDED from active.year — students can only be blocked
// at activation time (Task 08), not at the login form level.
Route::middleware(['guest:student', 'throttle:10,1'])->group(function () {
    Route::get('/student/activate',  [StudentAuthController::class, 'showActivateForm'])->name('student.activate');
    Route::post('/student/activate', [StudentAuthController::class, 'activate'])->name('student.activate.submit');
    Route::get('/student/login',     [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student/login',    [StudentAuthController::class, 'login'])->name('student.login.submit');
});

Route::middleware(['auth:student', 'student.year.active'])->group(function () {
    Route::post('/student/logout',    [StudentAuthController::class, 'logout'])->name('student.logout');

    // Student dashboard requires an active year
    Route::middleware('active.year')->group(function () {
        Route::get('/student/dashboard', fn () => view('student.dashboard'))->name('student.dashboard');
    });
});

// ─── Sprint 2: Doctor Level Routes ────────────────────────────────────────────
// Appended below — existing routes above are never modified.
// doctor.level middleware handles: active year check + assignment verification.

use App\Http\Controllers\Doctor\StudentController;
use App\Http\Controllers\Doctor\ProjectIdeaController;

Route::middleware(['auth', 'role:doctor', 'active.year', 'doctor.level'])
    ->prefix('doctor/{level}')
    ->name('doctor.')
    ->group(function () {

        // ── Students ─────────────────────────────────────────────────────────
        Route::get('/students',              [StudentController::class, 'index'])     ->name('students.index');
        Route::get('/students/import',       [StudentController::class, 'showImport'])->name('students.import');
        Route::post('/students/import',      [StudentController::class, 'import'])    ->name('students.import.store');
        Route::get('/students/export',       [StudentController::class, 'export'])    ->name('students.export');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])   ->name('students.destroy');
        Route::post('/students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore');

        // ── Project Ideas ─────────────────────────────────────────────────────
        Route::get('/ideas',             [ProjectIdeaController::class, 'index']) ->name('ideas.index');
        Route::get('/ideas/create',      [ProjectIdeaController::class, 'create'])->name('ideas.create');
        Route::post('/ideas',            [ProjectIdeaController::class, 'store']) ->name('ideas.store');
        Route::get('/ideas/{idea}/edit', [ProjectIdeaController::class, 'edit'])  ->name('ideas.edit');
        Route::put('/ideas/{idea}',      [ProjectIdeaController::class, 'update'])->name('ideas.update');
        Route::delete('/ideas/{idea}',   [ProjectIdeaController::class, 'destroy'])->name('ideas.destroy');
    });

// ─── Sprint 3: Teams, Distribution & Requests (Doctor) ───────────────────────

use App\Http\Controllers\Doctor\TeamController;
use App\Http\Controllers\Doctor\TeamDistributionController;
use App\Http\Controllers\Doctor\TeamRequestController;
use App\Http\Controllers\Student\TeamController as StudentTeamController;

Route::middleware(['auth', 'role:doctor', 'active.year', 'doctor.level'])
    ->prefix('doctor/{level}')
    ->name('doctor.')
    ->group(function () {

        // ── Teams CRUD ────────────────────────────────────────────────────────
        Route::get('/teams',                       [TeamController::class, 'index'])  ->name('teams.index');
        Route::get('/teams/distribute',            [TeamDistributionController::class, 'showForm']) ->name('teams.distribute');
        Route::post('/teams/distribute/preview',   [TeamDistributionController::class, 'preview'])  ->name('teams.distribute.preview');
        Route::post('/teams/distribute/confirm',   [TeamDistributionController::class, 'confirm'])  ->name('teams.distribute.confirm');
        Route::get('/teams/create',                [TeamController::class, 'create']) ->name('teams.create');
        Route::post('/teams',                      [TeamController::class, 'store'])  ->name('teams.store');
        Route::get('/teams/{team}/edit',           [TeamController::class, 'edit'])   ->name('teams.edit');
        Route::post('/teams/{team}',               [TeamController::class, 'update']) ->name('teams.update');
        Route::post('/teams/{team}/remove/{student}', [TeamController::class, 'removeMember'])->name('teams.remove_member');
        Route::post('/teams/{team}/delete',        [TeamController::class, 'destroy'])->name('teams.destroy');

        // ── Requests ──────────────────────────────────────────────────────────
        Route::get('/requests',                         [TeamRequestController::class, 'index'])  ->name('requests.index');
        Route::post('/requests/{teamRequest}/approve',  [TeamRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{teamRequest}/reject',   [TeamRequestController::class, 'reject']) ->name('requests.reject');
    });

// ─── Sprint 3: Student Team Routes ────────────────────────────────────────────

Route::middleware(['auth:student', 'student.year.active', 'active.year'])
    ->group(function () {
        Route::get('/student/team',         [StudentTeamController::class, 'show'])          ->name('student.team.show');
        Route::post('/student/team/request',[StudentTeamController::class, 'submitRequest']) ->name('student.team.request');
    });

