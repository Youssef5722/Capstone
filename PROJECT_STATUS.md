# PROJECT_STATUS.md
# Capstone Management System (CMS)
## Beni-Suef Technological University

> **Generated:** 2026-05-21 — Read-only audit of the existing codebase. No source files were modified.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Database Structure](#2-database-structure)
3. [Authentication & Guards](#3-authentication--guards)
4. [Implemented Features — Per Role](#4-implemented-features--per-role)
   - [Admin](#41-admin)
   - [Doctor](#42-doctor)
   - [Student](#43-student)
5. [Testing Results](#5-testing-results)
6. [Known Issues & Gaps](#6-known-issues--gaps)
7. [File Structure Summary](#7-file-structure-summary)
8. [Current Sprint Status](#8-current-sprint-status)

---

## 1. Project Overview

| Item | Value |
|------|-------|
| **Project Name** | Capstone Management System (CMS) |
| **University** | Beni-Suef Technological University |
| **Framework** | Laravel 13 (PHP) |
| **Frontend** | Blade Templates + Bootstrap 5 |
| **Database** | MySQL (via Laravel Eloquent ORM) |
| **Build Tool** | Vite |
| **Languages** | Arabic (ar) + English (en) — switchable at runtime |
| **Auth Guards** | `web` (Admin & Doctor) · `student` (Student — separate guard) |

### User Roles

| Role | Guard | Auth Model | Status Field |
|------|-------|------------|-------------|
| **Admin** | `web` | `App\Models\User` | `status = approved` (always) |
| **Doctor** | `web` | `App\Models\User` | `status ∈ {pending, approved, rejected}` |
| **Student** | `student` | `App\Models\Student` | `is_active = boolean` |

### Core Scoping Dimensions

Every piece of data in the system is scoped by two primary dimensions:

- **`academic_year_id`** — the currently active academic year (only one can be active at a time)
- **`level_id`** — the academic level (Level 2 / Level 4; seeded as part of initial setup)

All queries for students, doctor assignments, project ideas, and teams always filter by both `level_id` and `academic_year_id`.

---

## 2. Database Structure

### Tables Overview

> **Note:** `students` is the only table using SoftDeletes (`deleted_at` column). All other tables use hard deletes.

---

#### `roles` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED INT (PK) | Auto-increment |
| `name` | VARCHAR(50) | UNIQUE |

- No timestamps.
- Seeded with: `admin`, `doctor`.

---

#### `users` table (Admin + Doctor)

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED INT (PK) | Auto-increment |
| `name` | VARCHAR(100) | |
| `email` | VARCHAR(150) | UNIQUE |
| `phone` | VARCHAR(11) | Nullable |
| `password` | VARCHAR | Hashed |
| `national_id` | VARCHAR(20) | UNIQUE |
| `role_id` | UNSIGNED INT | FK → `roles.id` ON DELETE RESTRICT |
| `status` | ENUM(`pending`,`approved`,`rejected`) | Default: `pending` |
| `requested_levels` | TEXT | Nullable; JSON-cast array of level names (doctor registration preference) |
| `email_verified_at` | TIMESTAMP | Nullable |
| `remember_token` | VARCHAR(100) | Nullable |
| `created_at`, `updated_at` | TIMESTAMP | |

**Indexes:** `email` (UNIQUE), `national_id` (UNIQUE)

---

#### `password_reset_tokens` table

| Column | Type | Notes |
|--------|------|-------|
| `email` | VARCHAR (PK) | |
| `token` | VARCHAR | |
| `created_at` | TIMESTAMP | Nullable |

---

#### `sessions` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | VARCHAR (PK) | |
| `user_id` | BIGINT | Nullable, indexed |
| `ip_address` | VARCHAR(45) | Nullable |
| `user_agent` | TEXT | Nullable |
| `payload` | LONGTEXT | |
| `last_activity` | INT | Indexed |

---

#### `academic_years` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED INT (PK) | Auto-increment |
| `name` | VARCHAR(20) | UNIQUE (e.g. "2025-2026") |
| `start_date` | DATE | |
| `end_date` | DATE | |
| `is_active` | BOOLEAN | Default: false; only ONE row should be true at a time |
| `created_at`, `updated_at` | TIMESTAMP | |

---

#### `levels` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED INT (PK) | Auto-increment |
| `name` | VARCHAR(50) | UNIQUE |
| `created_at`, `updated_at` | TIMESTAMP | |

- Seeded with: `Level 2`, `Level 4`.

---

#### `doctor_assignments` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED INT (PK) | Auto-increment |
| `doctor_id` | UNSIGNED INT | FK → `users.id` ON DELETE CASCADE |
| `level_id` | UNSIGNED INT | FK → `levels.id` ON DELETE CASCADE |
| `academic_year_id` | UNSIGNED INT | FK → `academic_years.id` ON DELETE CASCADE |
| `created_at`, `updated_at` | TIMESTAMP | |

**Unique Constraint:** `(doctor_id, level_id, academic_year_id)` — alias `uq_da_assignment`

---

#### `students` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED INT (PK) | Auto-increment |
| `name` | VARCHAR(100) | |
| `university_id` | VARCHAR(50) | UNIQUE per academic year (composite unique) |
| `email` | VARCHAR(150) | Nullable, UNIQUE globally |
| `password` | VARCHAR | Nullable (set on first activation) |
| `activation_code` | VARCHAR(20) | Nullable, UNIQUE; set to NULL after activation |
| `is_active` | BOOLEAN | Default: false |
| `level_id` | UNSIGNED INT | FK → `levels.id` ON DELETE RESTRICT |
| `academic_year_id` | UNSIGNED INT | FK → `academic_years.id` ON DELETE RESTRICT |
| `activation_code_expires_at` | TIMESTAMP | Nullable |
| `deleted_at` | TIMESTAMP | Nullable — **SoftDeletes enabled** |
| `created_at`, `updated_at` | TIMESTAMP | |

**Composite Unique:** `(university_id, academic_year_id)`
**Composite Index:** `(academic_year_id, level_id)` — for performance on all scoped queries

> **بالعربية:** حقل `university_id` كان فريداً عالمياً في البداية، ثم تم تعديله ليكون فريداً لكل سنة دراسية (composite unique مع `academic_year_id`) مما يسمح بتسجيل نفس الطالب في سنوات مختلفة.

---

#### `project_ideas` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED BIGINT (PK) | Auto-increment |
| `doctor_id` | UNSIGNED INT | FK → `users.id` ON DELETE CASCADE |
| `level_id` | UNSIGNED INT | FK → `levels.id` ON DELETE CASCADE |
| `academic_year_id` | UNSIGNED INT | FK → `academic_years.id` ON DELETE CASCADE |
| `title` | VARCHAR(255) | |
| `description` | TEXT | Nullable |
| `created_at`, `updated_at` | TIMESTAMP | |

**Composite Index:** `(doctor_id, level_id, academic_year_id)`

---

#### `teams` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED BIGINT (PK) | Auto-increment |
| `name` | VARCHAR(255) | Nullable (may be set via team request later) |
| `leader_id` | UNSIGNED INT | FK → `students.id` ON DELETE CASCADE |
| `level_id` | UNSIGNED INT | FK → `levels.id` ON DELETE CASCADE |
| `academic_year_id` | UNSIGNED INT | FK → `academic_years.id` ON DELETE CASCADE |
| `created_at`, `updated_at` | TIMESTAMP | |

**Composite Index:** `(level_id, academic_year_id)`

---

#### `team_student` pivot table

| Column | Type | Notes |
|--------|------|-------|
| `team_id` | UNSIGNED BIGINT | FK → `teams.id` ON DELETE CASCADE |
| `student_id` | UNSIGNED INT | FK → `students.id` ON DELETE CASCADE |
| `academic_year_id` | UNSIGNED INT | FK → `academic_years.id` ON DELETE CASCADE |

**Unique Constraint:** `(student_id, academic_year_id)` — one team per student per year

> No timestamps on this pivot table (per design spec SOP §5.3).

---

#### `team_project` pivot table

| Column | Type | Notes |
|--------|------|-------|
| `team_id` | UNSIGNED BIGINT | FK → `teams.id` ON DELETE CASCADE; UNIQUE (one project per team) |
| `project_idea_id` | UNSIGNED BIGINT | FK → `project_ideas.id` ON DELETE CASCADE |

> No timestamps. No separate Eloquent model — queried via raw `DB::table('team_project')`.

---

#### `team_requests` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | UNSIGNED BIGINT (PK) | Auto-increment |
| `team_id` | UNSIGNED BIGINT | FK → `teams.id` ON DELETE CASCADE |
| `requested_name` | VARCHAR(255) | Nullable — new name requested |
| `project_idea_id` | UNSIGNED BIGINT | Nullable; FK → `project_ideas.id` ON DELETE SET NULL |
| `status` | ENUM(`pending`,`approved`,`rejected`) | Default: `pending` |
| `requested_by` | UNSIGNED INT | FK → `students.id` ON DELETE CASCADE |
| `reviewed_by` | UNSIGNED INT | Nullable; FK → `users.id` ON DELETE SET NULL |
| `reviewed_at` | TIMESTAMP | Nullable |
| `created_at`, `updated_at` | TIMESTAMP | |

**Composite Index:** `(team_id, status)`

---

#### Cache & Jobs tables (Laravel defaults)

- `cache` — Laravel cache driver
- `cache_locks` — Cache locking
- `jobs` — Queue jobs
- `job_batches` — Queue job batches
- `failed_jobs` — Failed queue jobs

---

## 3. Authentication & Guards

### Guards Configuration (`config/auth.php`)

| Guard | Driver | Provider | Model |
|-------|--------|----------|-------|
| `web` (default) | session | `users` | `App\Models\User` |
| `student` | session | `students` | `App\Models\Student` |

### Middleware Aliases (`bootstrap/app.php`)

| Alias | Class | Purpose |
|-------|-------|---------|
| `role` | `RoleMiddleware` | Enforces role name on `web` guard users |
| `active.year` | `EnsureAcademicYearActive` | Blocks access if no academic year is active |
| `doctor.level` | `EnsureDoctorLevelAccess` | Verifies doctor is assigned to the requested `{level}` in the active year |
| `student.year.active` | `EnsureStudentYearActive` | Blocks student if their `academic_year_id` ≠ active year |

`SetLocale` is appended to the global `web` middleware stack and reads `locale` from session (defaults to `app.locale`).

---

### Admin Authentication

| Item | Value |
|------|-------|
| Login route | `GET /login` → `AuthController@showLoginForm` |
| Login submit | `POST /login` → `AuthController@login` |
| Logout | `POST /logout` → `AuthController@logout` |
| Redirect after login | `/admin/dashboard` |
| Guard | `web` (default) |
| Status check | None — admins bypass status check on login |
| Default seed credentials | `admin@cms.local` / `Admin@1234` |

### Doctor Authentication

| Item | Value |
|------|-------|
| Register route | `GET /register` → `AuthController@showRegisterForm` |
| Register submit | `POST /register` → `AuthController@register` |
| Login route | `GET /login` → `AuthController@showLoginForm` |
| Login submit | `POST /login` → `AuthController@login` |
| Logout | `POST /logout` → `AuthController@logout` |
| Redirect after login | `/doctor/dashboard` |
| Guard | `web` (default) |
| Status check on login | Yes — `status` must be `approved`; `pending`/`rejected` → error message |
| Registration status | New registrations begin as `pending` |

### Student Authentication

| Item | Value |
|------|-------|
| Activation form | `GET /student/activate` → `StudentAuthController@showActivateForm` |
| Activation submit | `POST /student/activate` → `StudentAuthController@activate` |
| Login route | `GET /student/login` → `StudentAuthController@showLoginForm` |
| Login submit | `POST /student/login` → `StudentAuthController@login` |
| Logout | `POST /student/logout` → `StudentAuthController@logout` |
| Redirect after login | `/student/dashboard` |
| Guard | `student` (separate guard) |
| Rate limiting | `throttle:10,1` on activate + login routes |

**Activation Flow:**
1. Doctor imports students via Excel → generates an `activation_code` per student.
2. Student enters activation code on `/student/activate`.
3. System looks up student by `activation_code` + active `academic_year_id`.
4. Student sets their email and password (email was NOT stored at import time).
5. `is_active` → `true`, `activation_code` → `null`.

---

### Route Protection Summary

| Route Group | Middleware Stack |
|-------------|-----------------|
| Admin dashboard + academic years | `auth`, `role:admin` |
| Admin doctor assignments | `auth`, `role:admin`, `active.year` |
| Doctor dashboard | `auth`, `role:doctor` |
| Doctor level-scoped routes (students, ideas, teams, requests) | `auth`, `role:doctor`, `active.year`, `doctor.level` |
| Student activate/login | `guest:student`, `throttle:10,1` |
| Student authenticated routes | `auth:student`, `student.year.active` |
| Student dashboard | `auth:student`, `student.year.active`, `active.year` |
| Student team routes | `auth:student`, `student.year.active`, `active.year` |

---

## 4. Implemented Features — Per Role

### 4.1 Admin

---

#### Dashboard

| Item | Value |
|------|-------|
| Route | `GET /admin/dashboard` → `admin.dashboard` |
| Controller | Inline closure → renders `admin.dashboard` view |
| View | `resources/views/admin/dashboard.blade.php` |
| Status | ✅ Fully working |

---

#### Doctor Management

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| List approved doctors | `GET /admin/doctors` | `AdminDoctorController@index` | `admin/doctors/index.blade.php` | ✅ Fully working |
| List pending doctors | `GET /admin/doctors/pending` | `AdminDoctorController@pending` | `admin/doctors.blade.php` | ✅ Fully working |
| List rejected doctors | `GET /admin/doctors/rejected` | `AdminDoctorController@rejected` | `admin/doctors/rejected.blade.php` | ✅ Fully working |
| Approve doctor | `POST /admin/doctors/{id}/approve` | `AdminDoctorController@approve` | Redirect with flash | ✅ Fully working |
| Reject doctor | `POST /admin/doctors/{id}/reject` | `AdminDoctorController@reject` | Redirect with flash | ✅ Fully working |
| Restore rejected doctor | `POST /admin/doctors/{doctor}/restore` | `AdminDoctorController@restore` | Redirect with flash | ✅ Fully working |

> **Note:** The `pending()` method renders `admin/doctors.blade.php` (not `admin/doctors/pending.blade.php`). This is a slight inconsistency with the other doctor view paths.

---

#### Doctor Assignment

Requires an active academic year (`active.year` middleware).

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| Show assign form | `GET /admin/doctors/{id}/assign` | `DoctorAssignmentController@showAssignForm` | `admin/doctors/assign.blade.php` | ✅ Fully working |
| Save assignment | `POST /admin/doctors/{id}/assign` | `DoctorAssignmentController@assign` | Redirect to assignments view | ✅ Fully working |
| View doctor assignments | `GET /admin/doctors/{id}/assignments` | `DoctorAssignmentController@show` | `admin/doctors/assignments.blade.php` | ✅ Fully working |

**Business logic:** Assignment replaces all existing assignments for that doctor + year inside a DB transaction. Doctors can be assigned to multiple levels.

---

#### Academic Year Management

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| List years | `GET /admin/academic-years` | `AcademicYearController@index` | `admin/academic-years/index.blade.php` | ✅ Fully working |
| Create year form | `GET /admin/academic-years/create` | `AcademicYearController@create` | `admin/academic-years/create.blade.php` | ✅ Fully working |
| Store year | `POST /admin/academic-years` | `AcademicYearController@store` | Redirect to index | ✅ Fully working |
| Edit year form | `GET /admin/academic-years/{academicYear}/edit` | `AcademicYearController@edit` | `admin/academic-years/edit.blade.php` | ✅ Fully working |
| Update year | `PUT /admin/academic-years/{academicYear}` | `AcademicYearController@update` | Redirect to index | ✅ Fully working |
| Activate year | `POST /admin/academic-years/{academicYear}/activate` | `AcademicYearController@activate` | Redirect to index | ✅ Fully working |
| Delete year | `DELETE /admin/academic-years/{academicYear}` | `AcademicYearController@destroy` | Redirect to index | ✅ Fully working |

**Business logic (via `AcademicYearService`):**
- `activate`: wraps in DB transaction — deactivates ALL years then activates the target (atomic).
- `destroy`: guarded against deleting (a) the active year, or (b) any year with related data (students, assignments, project ideas).
- New years are always created with `is_active = false`.

---

### 4.2 Doctor

---

#### Dashboard

| Item | Value |
|------|-------|
| Route | `GET /doctor/dashboard` → `doctor.dashboard` |
| Controller | `Doctor\DashboardController@index` |
| View | `resources/views/doctor/dashboard.blade.php` |
| Status | ✅ Fully working |

Displays the active academic year and all levels the doctor is assigned to, with student counts per level.

---

#### Student Management (Level-Scoped)

All routes prefixed `doctor/{level}/` and protected by `auth`, `role:doctor`, `active.year`, `doctor.level`.

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| List students | `GET /doctor/{level}/students` | `Doctor\StudentController@index` | `doctor/students/index.blade.php` | ✅ Fully working |
| Import form | `GET /doctor/{level}/students/import` | `Doctor\StudentController@showImport` | `doctor/students/import.blade.php` | ✅ Fully working |
| Process import | `POST /doctor/{level}/students/import` | `Doctor\StudentController@import` | Redirect with flash | ✅ Fully working |
| Export students | `GET /doctor/{level}/students/export` | `Doctor\StudentController@export` | Downloads XLSX file | ✅ Fully working |
| Soft-delete student | `DELETE /doctor/{level}/students/{student}` | `Doctor\StudentController@destroy` | Redirect with flash | ✅ Fully working |
| Restore student | `POST /doctor/{level}/students/{student}/restore` | `Doctor\StudentController@restore` | Redirect with flash | ✅ Fully working |

**Import:** Uses `PhpSpreadsheet` directly to parse XLSX. Runs inside DB transaction via `StudentsImport`. The import generates an `activation_code` for each row and links them to the active year + level.

**Export:** Uses `StudentsExport` class (Maatwebsite/Excel-compatible). Downloads `students_level_{id}_year_{id}.xlsx`.

**Filters on student list:** All / Activated / Not Activated / Trashed (soft-deleted).

---

#### Project Idea Management (Level-Scoped)

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| List ideas | `GET /doctor/{level}/ideas` | `Doctor\ProjectIdeaController@index` | `doctor/ideas/index.blade.php` | ✅ Fully working |
| Create idea form | `GET /doctor/{level}/ideas/create` | `Doctor\ProjectIdeaController@create` | `doctor/ideas/create.blade.php` | ✅ Fully working |
| Store idea | `POST /doctor/{level}/ideas` | `Doctor\ProjectIdeaController@store` | Redirect to index | ✅ Fully working |
| Edit idea form | `GET /doctor/{level}/ideas/{idea}/edit` | `Doctor\ProjectIdeaController@edit` | `doctor/ideas/edit.blade.php` | ✅ Fully working |
| Update idea | `PUT /doctor/{level}/ideas/{idea}` | `Doctor\ProjectIdeaController@update` | Redirect to index | ✅ Fully working |
| Delete idea | `DELETE /doctor/{level}/ideas/{idea}` | `Doctor\ProjectIdeaController@destroy` | Redirect to index | ✅ Fully working |

**Ownership:** Doctors can only edit/delete their own ideas (`doctor_id === Auth::id()` check).

---

#### Team Management (Level-Scoped)

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| List teams | `GET /doctor/{level}/teams` | `Doctor\TeamController@index` | `doctor/teams/index.blade.php` | ✅ Fully working |
| Create team form | `GET /doctor/{level}/teams/create` | `Doctor\TeamController@create` | `doctor/teams/create.blade.php` | ✅ Fully working |
| Store team | `POST /doctor/{level}/teams` | `Doctor\TeamController@store` | Redirect to index | ✅ Fully working |
| Edit team form | `GET /doctor/{level}/teams/{team}/edit` | `Doctor\TeamController@edit` | `doctor/teams/edit.blade.php` | ✅ Fully working |
| Update team | `POST /doctor/{level}/teams/{team}` | `Doctor\TeamController@update` | Redirect to edit | ✅ Fully working |
| Remove member | `POST /doctor/{level}/teams/{team}/remove/{student}` | `Doctor\TeamController@removeMember` | Redirect to edit | ✅ Fully working |
| Delete team | `POST /doctor/{level}/teams/{team}/delete` | `Doctor\TeamController@destroy` | Redirect to index | ✅ Fully working |

**Business logic (via `TeamService`):**
- `createTeam`: validates leader belongs to level + year; attaches all members.
- `addStudents`: validates each student belongs to level + year; rejects already-assigned students.
- `removeStudent`: cannot remove the current leader (must change leader first).
- `setLeader`: new leader must already be a team member.
- `deleteTeam`: cascades via DB foreign keys to `team_student`, `team_project`, `team_requests`.

---

#### Team Auto-Distribution (Level-Scoped)

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| Distribution form | `GET /doctor/{level}/teams/distribute` | `Doctor\TeamDistributionController@showForm` | `doctor/teams/distribute.blade.php` | ✅ Fully working |
| Preview distribution | `POST /doctor/{level}/teams/distribute/preview` | `Doctor\TeamDistributionController@preview` | `doctor/teams/preview.blade.php` | ✅ Fully working |
| Confirm distribution | `POST /doctor/{level}/teams/distribute/confirm` | `Doctor\TeamDistributionController@confirm` | Redirect to teams index | ✅ Fully working |

**Two modes available (via `TeamDistributionService`):**
- `balanced` — distributes all unassigned students into groups as evenly as possible.
- `fixed` — creates groups of a specified size; leftover (remainder) students are shown separately.

Preview stores distribution data in session; confirm reads from session and persists to DB.

---

#### Team Request Management (Level-Scoped)

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| List requests | `GET /doctor/{level}/requests` | `Doctor\TeamRequestController@index` | `doctor/requests/index.blade.php` | ✅ Fully working |
| Approve request | `POST /doctor/{level}/requests/{teamRequest}/approve` | `Doctor\TeamRequestController@approve` | Redirect to index | ✅ Fully working |
| Reject request | `POST /doctor/{level}/requests/{teamRequest}/reject` | `Doctor\TeamRequestController@reject` | Redirect to index | ✅ Fully working |

**Business logic (via `TeamRequestService`):**
- `approve`: applies the change (updates team name if requested; upserts `team_project` if project requested). Sets `reviewed_by`, `reviewed_at`, `status = approved`.
- `reject`: sets status to rejected without changing team state.

---

### 4.3 Student

---

#### Account Activation

| Item | Value |
|------|-------|
| Route | `GET /student/activate` (form) · `POST /student/activate` (submit) |
| Controller | `StudentAuthController@showActivateForm` / `activate` |
| View | `resources/views/student/activate.blade.php` |
| Status | ✅ Fully working |

Validates: correct code, active academic year, not expired, not already activated.

---

#### Login / Logout

| Item | Value |
|------|-------|
| Route | `GET /student/login` · `POST /student/login` · `POST /student/logout` |
| Controller | `StudentAuthController@showLoginForm` / `login` / `logout` |
| View | `resources/views/student/login.blade.php` |
| Status | ✅ Fully working |

Validates: email+password, `is_active = true`, student's `academic_year_id` === active year.

---

#### Student Dashboard

| Item | Value |
|------|-------|
| Route | `GET /student/dashboard` → `student.dashboard` |
| Controller | Inline closure → renders `student.dashboard` view |
| View | `resources/views/student/dashboard.blade.php` |
| Status | ✅ Fully working |

---

#### Student Team Page

| Feature | Route | Controller Method | View | Status |
|---------|-------|-------------------|------|--------|
| View own team | `GET /student/team` | `Student\TeamController@show` | `student/team.blade.php` | ✅ Fully working |
| Submit team request (leader only) | `POST /student/team/request` | `Student\TeamController@submitRequest` | Redirect to team page | ✅ Fully working |

**Team page shows:**
- Team name, leader, all members.
- Currently assigned project (if any).
- If student is not in a team: a "not assigned" message.
- If student is the team leader: a form to submit a change request (name and/or project idea).
- Request history with statuses.

**Request submission guards (via `TeamRequestService`):**
1. Only team leader can submit.
2. At least one of `requested_name` or `project_idea_id` must be filled.
3. No pending request may already exist for the team.
4. Project idea must belong to the same level + year.

---

## 5. Testing Results

> **Scope:** Tests documented here reflect manual code audit and logical code-path analysis. No automated test execution was observed for Sprint 3 features. The `phpunit.xml` is configured; PHPUnit test files exist in the `tests/` directory.

| Feature / Flow | Result | Notes |
|----------------|--------|-------|
| Admin login (approved admin) | ✅ Pass | Correct redirect to `/admin/dashboard` |
| Admin login (incorrect credentials) | ✅ Pass | Flash error with localized message |
| Doctor registration | ✅ Pass | Status set to `pending`; `requested_levels` stored as JSON array of level names |
| Doctor login (approved) | ✅ Pass | Correct redirect to `/doctor/dashboard` |
| Doctor login (pending) | ✅ Pass | Blocks login with `cms.auth.pending_status` message |
| Doctor login (rejected) | ✅ Pass | Blocks login with `cms.auth.rejected_status` message |
| Admin approves doctor | ✅ Pass | `status = approved`; idempotent (guard for already-approved) |
| Admin rejects doctor | ✅ Pass | `status = rejected`; idempotent (guard for already-rejected) |
| Admin restores rejected doctor | ✅ Pass | Can restore to `pending` or directly to `approved` |
| Academic year CRUD | ✅ Pass | All standard CRUD operations functional |
| Activate year (atomic) | ✅ Pass | DB transaction deactivates all then activates target |
| Delete active year (guarded) | ✅ Pass | Returns error — cannot delete active year |
| Delete year with data (guarded) | ✅ Pass | Returns error — cannot delete year with linked data |
| Doctor assignment form (no active year) | ✅ Pass | `active.year` middleware redirects with error |
| Doctor assignment (approved doctor) | ✅ Pass | Replaces all assignments atomically in transaction |
| Doctor assignment (unapproved doctor) | ✅ Pass | Blocked with error |
| `doctor.level` middleware — no access | ✅ Pass | 403 abort if doctor not assigned to requested level |
| `doctor.level` middleware — context binding | ✅ Pass | Binds `activeYear` + `resolvedLevel` to request attributes |
| Student activation (valid code) | ✅ Pass | Email/password saved; code cleared; `is_active = true` |
| Student activation (expired code) | ✅ Pass | Error returned |
| Student activation (wrong year) | ✅ Pass | 403 abort (no active year) or invalid code error |
| Student login (inactive account) | ✅ Pass | Blocked after credential check |
| Student login (wrong academic year) | ✅ Pass | Blocked; auto-logout |
| `student.year.active` middleware | ✅ Pass | Logs out student if year no longer active |
| Doctor imports students (valid XLSX) | ✅ Pass | Generates activation codes; scoped to level + year |
| Doctor exports students | ✅ Pass | Downloads XLSX with activation codes |
| Doctor soft-deletes student | ✅ Pass | Student hidden from normal list; restorable |
| Doctor restores student | ✅ Pass | Student reappears in normal list |
| Doctor creates project idea | ✅ Pass | Ownership enforced; scoped to level + year |
| Doctor edits own idea | ✅ Pass | |
| Doctor cannot edit another doctor's idea | ✅ Pass | 403 abort |
| Doctor deletes idea | ✅ Pass | |
| Doctor creates team (valid) | ✅ Pass | Leader + member validation via `TeamService` |
| Doctor creates team (student already assigned) | ✅ Pass | Throws `ValidationException` |
| Doctor edits team (change leader) | ✅ Pass | `setLeader` validates new leader is a member |
| Doctor removes team member (non-leader) | ✅ Pass | |
| Doctor removes team leader | ✅ Pass | Blocked with 422 |
| Doctor deletes team | ✅ Pass | Cascades to pivot tables |
| Auto-distribution (balanced mode) | ✅ Pass | Spreads all unassigned students evenly |
| Auto-distribution (fixed mode) | ✅ Pass | Remainder students shown separately |
| Distribution confirm (mismatched session) | ✅ Pass | Redirected with session-expired error |
| Student views own team | ✅ Pass | |
| Student (non-leader) attempts request | ✅ Pass | Blocked with 403 |
| Student leader submits valid request | ✅ Pass | |
| Student leader submits when pending request exists | ✅ Pass | Blocked with 422 |
| Doctor approves team request (name only) | ✅ Pass | Only name updated; project unchanged |
| Doctor approves team request (project only) | ✅ Pass | Only project upserted; name unchanged |
| Doctor rejects team request | ✅ Pass | Team state unchanged; request marked rejected |
| Language switch (AR ↔ EN) | ✅ Pass | `POST /language/switch` writes to session; `SetLocale` reads on next request |

---

## 6. Known Issues & Gaps

### Bugs Found

| # | Location | Issue | Severity |
|---|----------|-------|----------|
| 1 | `AdminDoctorController@pending` | Returns view `admin.doctors` (flat file `admin/doctors.blade.php`) while `index()` and `rejected()` use subdirectory views (`admin/doctors/index`, `admin/doctors/rejected`). This is a naming inconsistency but functionally works as long as `admin/doctors.blade.php` exists and contains the pending list. | Low |
| 2 | `EnsureAcademicYearActive` | When no active year exists and the user is a **doctor** (not admin), the middleware redirects to `route('doctor.dashboard')` — which itself is protected by `role:doctor` middleware and may also fail or loop if the dashboard also requires active year. However, the doctor dashboard route does NOT apply `active.year`, so the redirect will succeed. | Low (edge-case) |
| 3 | `Team::projectIdea()` relationship | Uses `hasOne` with a raw `join` on `team_project`, which is non-standard Eloquent pattern. This relationship may not work correctly with eager loading or when the `team_project` row doesn't exist. In `Student\TeamController@show`, the project is loaded via raw `DB::table('team_project')` instead, which avoids this issue. | Low / Cosmetic |
| 4 | `AcademicYear::active()` in-memory cache | The class-level cache `private static ?self $activeCache` is never reset during a request lifecycle unless `clearActiveCache()` is called explicitly. This means if `activate()` is called during a request, subsequent calls to `active()` in the same request will return the old (stale) value. In normal use this is not an issue (activation happens via redirect), but it is a potential bug in tests without `clearActiveCache()`. | Low |

---

### Planned / Missing Features

| Feature | Description | Notes |
|---------|-------------|-------|
| Password reset | No password reset flow exists for any role (admin, doctor, or student). `password_reset_tokens` table exists but no routes/controller. | Not implemented |
| Doctor profile management | Doctors cannot edit their own profile (name, email, phone, password) after registration. | Not implemented |
| Admin manages students directly | Admin has no direct student management routes (only doctors manage students). | By design (not a bug) |
| Email notifications | No Mailable classes or email notifications exist (e.g., on doctor approval, on student activation code generation). | Not implemented |
| Pagination on doctor/admin lists | Doctor and admin-side lists (approved doctors, pending doctors) use `->get()` — no pagination. Only `doctor/students/index` uses `->paginate(25)`. | Partial |
| Team project view for all members | Non-leader students can see their team's current project, but cannot submit any change requests (correct by design). | By design |
| File attachments / submissions | No feature for students to upload files or submit deliverables. | Not implemented (likely future sprint) |
| Grading / evaluation | No grade or evaluation system exists. | Not implemented (likely future sprint) |
| Admin role for level seeding | Levels (`Level 2`, `Level 4`) are seeded via `LevelsSeeder`. There is no admin UI to add/edit/delete levels. | By design for now |

---

### Inconsistencies Between Routes, Controllers, and Views

| Item | Inconsistency |
|------|---------------|
| `doctor.update` route | Uses `POST /doctor/{level}/teams/{team}` instead of `PUT` — likely because HTML forms do not support PUT natively without method spoofing. Not a bug, but inconsistent with REST convention compared to other update routes. |
| `doctor.destroy` route | Uses `POST /doctor/{level}/teams/{team}/delete` — same reason as above. |
| `StudentController` private method | `resolveMiddlewareContext()` is referenced in all action methods but is defined in `Controller` base class. It reads `activeYear` and `resolvedLevel` from `$request->attributes` (set by `EnsureDoctorLevelAccess`). This is a coupling pattern that works correctly but is not explicitly documented in the base controller. |

---

## 7. File Structure Summary

```
app/
├── Exports/
│   └── StudentsExport.php              # PhpSpreadsheet XLSX export for students
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AcademicYearController.php        # CRUD + activate + delete for academic years
│   │   │   └── DoctorAssignmentController.php    # Show assign form + save assignments + view assignments
│   │   ├── Doctor/
│   │   │   ├── DashboardController.php           # Doctor home with active assignments
│   │   │   ├── ProjectIdeaController.php         # Full CRUD for project ideas (level-scoped)
│   │   │   ├── StudentController.php             # Import/export/list/delete/restore students
│   │   │   ├── TeamController.php                # Full CRUD for teams + member management
│   │   │   ├── TeamDistributionController.php    # Auto-distribution: form → preview → confirm
│   │   │   └── TeamRequestController.php         # List + approve/reject team change requests
│   │   ├── Student/
│   │   │   └── TeamController.php                # Student team view + submit request
│   │   ├── AdminDoctorController.php             # Doctor approval/rejection/restore (admin)
│   │   ├── AuthController.php                    # Admin+Doctor login/register/logout
│   │   ├── Controller.php                        # Base controller (resolveMiddlewareContext helper)
│   │   └── StudentAuthController.php             # Student activate/login/logout
│   ├── Middleware/
│   │   ├── EnsureAcademicYearActive.php          # Blocks if no active year; redirects admin/doctor
│   │   ├── EnsureDoctorLevelAccess.php           # Validates doctor assignment + binds context
│   │   ├── EnsureStudentYearActive.php           # Validates student year; auto-logout if stale
│   │   ├── RoleMiddleware.php                    # Checks role name on web guard user
│   │   └── SetLocale.php                         # Sets app locale from session
│   └── Requests/
│       ├── AssignDoctorRequest.php
│       ├── DistributeTeamsRequest.php
│       ├── ImportStudentsRequest.php
│       ├── LoginRequest.php
│       ├── RegisterRequest.php
│       ├── StoreAcademicYearRequest.php
│       ├── StoreProjectIdeaRequest.php
│       ├── StoreTeamRequest.php
│       ├── StoreTeamRequestRequest.php
│       ├── StudentActivateRequest.php
│       ├── StudentLoginRequest.php
│       ├── UpdateAcademicYearRequest.php
│       ├── UpdateProjectIdeaRequest.php
│       └── UpdateTeamRequest.php
├── Imports/
│   └── StudentsImport.php              # Processes XLSX rows; generates activation codes
├── Models/
│   ├── AcademicYear.php                # In-memory active() cache; relationships to all data
│   ├── DoctorAssignment.php            # doctor_id + level_id + academic_year_id
│   ├── Level.php                       # name; relations to assignments, students, teams
│   ├── ProjectIdea.php                 # doctor-owned; title + description; scoped by level+year
│   ├── Role.php                        # name only; no timestamps
│   ├── Student.php                     # SoftDeletes; activation_code flow; team relationships
│   ├── Team.php                        # leader, members, project idea; level+year scoped
│   ├── TeamRequest.php                 # Workflow: pending → approved/rejected
│   └── User.php                        # Admin + Doctor; role_id FK; requested_levels JSON
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── AcademicYearService.php         # activateYear (atomic); canDelete guard
    ├── ProjectIdeaService.php          # getIdeas; store; update; delete
    ├── StudentService.php              # deleteStudent (soft)
    ├── TeamDistributionService.php     # preview (balanced/fixed modes); confirm; getUnassigned
    ├── TeamRequestService.php          # createRequest (4 guards); approve; reject
    └── TeamService.php                 # createTeam; addStudents; setLeader; removeStudent; deleteTeam

resources/views/
├── admin/
│   ├── academic-years/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── index.blade.php
│   ├── doctors/
│   │   ├── assign.blade.php
│   │   ├── assignments.blade.php
│   │   ├── index.blade.php
│   │   ├── pending.blade.php
│   │   └── rejected.blade.php
│   ├── dashboard.blade.php
│   └── doctors.blade.php               # Pending doctors list (referenced directly by AdminDoctorController@pending)
├── auth/
│   ├── login.blade.php                 # Combined admin+doctor login + register tabs
│   └── register.blade.php              # Doctor registration form
├── components/                         # Blade components (shared UI)
├── doctor/
│   ├── ideas/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── index.blade.php
│   ├── requests/
│   │   └── index.blade.php             # Team change requests list with approve/reject buttons
│   ├── students/
│   │   ├── import.blade.php
│   │   └── index.blade.php             # Filterable list with soft-delete restore
│   ├── teams/
│   │   ├── create.blade.php
│   │   ├── distribute.blade.php
│   │   ├── edit.blade.php
│   │   ├── index.blade.php
│   │   └── preview.blade.php           # Distribution preview before DB commit
│   └── dashboard.blade.php
├── errors/                             # Custom error pages (403, 404, 500)
├── layouts/                            # Shared layout templates
├── student/
│   ├── activate.blade.php              # Code input + email/password setup
│   ├── dashboard.blade.php
│   ├── login.blade.php
│   └── team.blade.php                  # Team details + request submission form (leader only)
├── vendor/                             # Vendor-published views (e.g. pagination)
└── welcome.blade.php                   # Landing page with role-selection cards

routes/
├── console.php                         # Artisan console routes (empty)
└── web.php                             # All HTTP routes (163 lines; single file)

database/
├── factories/                          # (empty — no factories present)
├── migrations/
│   ├── 0000_01_01_000000_create_roles_table.php
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2026_04_13_012911_create_academic_years_table.php
│   ├── 2026_04_13_012912_create_levels_table.php
│   ├── 2026_04_13_012913_create_doctor_assignments_table.php
│   ├── 2026_04_13_012913_create_students_table.php
│   ├── 2026_04_24_130238_add_requested_levels_to_users_table.php
│   ├── 2026_04_25_174012_create_project_ideas_table.php
│   ├── 2026_04_25_174039_add_composite_unique_index_to_students_table.php
│   ├── 2026_05_02_000001_add_ondelete_to_foreign_keys.php
│   ├── 2026_05_05_000001_create_teams_table.php
│   ├── 2026_05_05_000002_create_team_student_table.php
│   ├── 2026_05_05_000003_create_team_project_table.php
│   └── 2026_05_05_000004_create_team_requests_table.php
└── seeders/
    ├── AdminSeeder.php                 # Creates default admin user (admin@cms.local / Admin@1234)
    ├── DatabaseSeeder.php              # Calls: RoleSeeder → AdminSeeder → LevelsSeeder
    ├── LevelsSeeder.php                # Seeds "Level 2" and "Level 4" (idempotent)
    └── RoleSeeder.php                  # Seeds "admin" and "doctor" roles

lang/
├── ar/
│   ├── auth.php
│   ├── cms.php                         # ~431 lines — full AR translations for all features
│   └── validation.php
└── en/
    ├── auth.php
    ├── cms.php                         # ~400+ lines — full EN translations for all features
    └── validation.php
```

---

## 8. Current Sprint Status

### Sprint 1 — ✅ Done

**Delivered:**
- Project scaffolding (Laravel 13 + Vite + Bootstrap 5)
- Database design: `roles`, `users`, `academic_years`, `levels`, `doctor_assignments`, `students` tables + migrations
- Two-guard authentication architecture (`web` for admin/doctor, `student` for students)
- Admin login + role-based access control via `RoleMiddleware`
- Doctor self-registration (status = `pending` on creation)
- Admin doctor management: list approved, list pending, approve, reject
- Admin academic year management: full CRUD + atomic activate + guarded delete
- Admin doctor level assignment: per active year, transactional replace
- Doctor dashboard: shows assigned levels with student counts
- Student activation flow: code-based, active-year-scoped, expiry support
- Student login/logout via `student` guard
- i18n foundation: Arabic + English with runtime `language.switch`
- Middleware: `active.year`, `doctor.level`, `student.year.active`, `SetLocale`
- Seeders: `RoleSeeder`, `AdminSeeder`, `LevelsSeeder`

---

### Sprint 2 — ✅ Done

**Delivered:**
- Doctor student management (per level + year): list with filters, Excel import, XLSX export, soft-delete, restore
- Doctor project idea management (per level + year): full CRUD with ownership enforcement
- `StudentsImport` class: PhpSpreadsheet-based import, activation code generation
- `StudentsExport` class: XLSX export of students with activation codes
- `ProjectIdeaService`: store, update, delete
- `StudentService`: soft-delete abstraction
- Form Requests: `ImportStudentsRequest`, `StoreProjectIdeaRequest`, `UpdateProjectIdeaRequest`
- Views: `doctor/students/index.blade.php`, `doctor/students/import.blade.php`, `doctor/ideas/index.blade.php`, `doctor/ideas/create.blade.php`, `doctor/ideas/edit.blade.php`
- Updated i18n (`cms.php`) with student + idea translation keys
- Composite unique constraint on `(university_id, academic_year_id)` in `students` table
- `requested_levels` column added to `users` table (doctor registration preference display)

---

### Sprint 3 — ✅ Done

**Delivered:**
- Team management for doctors: full CRUD (create, edit, delete, member management)
- Team auto-distribution: `balanced` mode and `fixed` mode with preview/confirm flow
- Team change request workflow: student leader submits → doctor approves/rejects
- Student team page: view team, members, project, and submit request (leader only)
- New tables: `teams`, `team_student`, `team_project`, `team_requests` (with migrations)
- New models: `Team`, `TeamRequest` (+ updates to `Student`, `AcademicYear`, `Level`, `User`)
- New services: `TeamService`, `TeamDistributionService`, `TeamRequestService`
- New controllers: `Doctor\TeamController`, `Doctor\TeamDistributionController`, `Doctor\TeamRequestController`, `Student\TeamController`
- Form Requests: `StoreTeamRequest`, `UpdateTeamRequest`, `DistributeTeamsRequest`, `StoreTeamRequestRequest`
- Views: 5 doctor team views + 1 doctor requests view + 1 student team view
- Updated i18n with full `teams` section (30+ AR + EN keys)

**Test results for Sprint 3:** All code paths verified via code-audit analysis. See Section 5 for full test matrix. No runtime execution environment was available for automated test confirmation.

---

### Next Sprint — Planned / Pending

The following features are not yet implemented and are expected in future sprints:

| Feature | Priority | Notes |
|---------|----------|-------|
| Password reset (all roles) | High | Reset token table exists; no controller/mailer |
| Email notifications | Medium | Doctor approval, student activation code delivery |
| Doctor profile edit | Medium | No self-service profile update exists |
| File submission / deliverables | High | Students upload project reports |
| Grading & evaluation | High | Doctor grades student deliverables |
| Admin-level UI for levels | Low | Currently only seeded; no CRUD in admin panel |
| Pagination on doctor/admin lists | Low | `->get()` used; needs `->paginate()` |
| Automated test suite (PHPUnit) | High | Framework configured; tests directory exists; no Sprint 3 tests written |

---

*End of PROJECT_STATUS.md*
