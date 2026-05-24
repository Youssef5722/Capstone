<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Level;
use App\Models\Student;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(private readonly TeamService $service) {}

    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $teams = Team::where('level_id', $level->id)
                     ->where('academic_year_id', $activeYear->id)
                     ->with(['leader', 'students'])
                     ->get();

        return view('doctor.teams.index', compact('level', 'activeYear', 'teams'));
    }

    // ── Create ─────────────────────────────────────────────────────────────────

    public function create(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        // Only unassigned students may be added to a new team
        $students = Student::where('level_id', $level->id)
                           ->where('academic_year_id', $activeYear->id)
                           ->whereDoesntHave('teams', fn ($q) =>
                               $q->where('team_student.academic_year_id', $activeYear->id)
                           )
                           ->get();

        return view('doctor.teams.create', compact('level', 'activeYear', 'students'));
    }

    // ── Store ──────────────────────────────────────────────────────────────────

    public function store(StoreTeamRequest $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $this->service->createTeam($request->validated(), $level, $activeYear);

        return redirect()
            ->route('doctor.teams.index', $level)
            ->with('success', __('cms.teams.created_success'));
    }

    // ── Edit ───────────────────────────────────────────────────────────────────

    public function edit(Request $request, Level $level, Team $team)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        // Ensure the team belongs to this level + year
        if ($team->level_id !== $level->id || $team->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $team->load(['leader', 'students']);

        // All students in the level+year (for adding new members)
        $students = Student::where('level_id', $level->id)
                           ->where('academic_year_id', $activeYear->id)
                           ->get();

        return view('doctor.teams.edit', compact('level', 'activeYear', 'team', 'students'));
    }

    // ── Update ─────────────────────────────────────────────────────────────────

    public function update(UpdateTeamRequest $request, Level $level, Team $team)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        if ($team->level_id !== $level->id || $team->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $data = $request->validated();

        // Update name (nullable is fine)
        $team->update(['name' => $data['name']]);

        // Change leader if different
        if ((int) $data['leader_id'] !== $team->leader_id) {
            $newLeader = Student::findOrFail($data['leader_id']);
            $this->service->setLeader($team, $newLeader);
        }

        // Add newly selected members (student_ids from the add-members sub-form)
        if ($request->filled('add_student_ids')) {
            $addIds = $request->input('add_student_ids');

            // Fix 10: detect students already in another team
            if ($request->boolean('confirm_transfer')) {
                // Doctor confirmed — transfer each student from their current team
                foreach ($addIds as $studentId) {
                    $student = Student::where('id', $studentId)
                                      ->where('level_id', $level->id)
                                      ->where('academic_year_id', $activeYear->id)
                                      ->firstOrFail();

                    $currentTeam = $student->teams()
                        ->where('team_student.academic_year_id', $activeYear->id)
                        ->first();

                    if ($currentTeam && $currentTeam->id !== $team->id) {
                        $this->service->transferStudent($student, $currentTeam, $team);
                    } elseif (!$currentTeam) {
                        $this->service->addStudents($team, [$studentId]);
                    }
                    // if already in this team, skip
                }
            } else {
                // Check if any selected student is already in a different team
                $transfers = [];
                $fresh = [];

                foreach ($addIds as $studentId) {
                    $student = Student::find($studentId);
                    if (!$student) continue;

                    $currentTeam = $student->teams()
                        ->where('team_student.academic_year_id', $activeYear->id)
                        ->first();

                    if ($currentTeam && $currentTeam->id !== $team->id) {
                        $transfers[] = ['student' => $student, 'from_team' => $currentTeam];
                    } else {
                        $fresh[] = $studentId;
                    }
                }

                // Add students not in any team normally
                if (!empty($fresh)) {
                    $this->service->addStudents($team, $fresh);
                }

                // If transfers needed — flash session and redirect to show modal
                if (!empty($transfers)) {
                    return redirect()
                        ->route('doctor.teams.edit', [$level, $team])
                        ->with('success', empty($fresh) ? null : __('cms.teams.updated_success'))
                        ->with('transfer_confirm', [
                            'students'       => $transfers,
                            'add_student_ids'=> $addIds,
                        ]);
                }
            }
        }

        return redirect()
            ->route('doctor.teams.edit', [$level, $team])
            ->with('success', __('cms.teams.updated_success'));
    }

    // ── Remove Member (sub-action on edit page) ────────────────────────────────

    public function removeMember(Request $request, Level $level, Team $team, Student $student)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        if ($team->level_id !== $level->id || $team->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $this->service->removeStudent($team, $student);

        return redirect()
            ->route('doctor.teams.edit', [$level, $team])
            ->with('success', __('cms.teams.member_removed'));
    }

    // ── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(Request $request, Level $level, Team $team)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        if ($team->level_id !== $level->id || $team->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $this->service->deleteTeam($team);

        return redirect()
            ->route('doctor.teams.index', $level)
            ->with('success', __('cms.teams.deleted_success'));
    }
}
