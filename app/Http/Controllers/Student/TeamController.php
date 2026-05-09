<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequestRequest;
use App\Models\AcademicYear;
use App\Models\ProjectIdea;
use App\Models\Team;
use App\Services\TeamRequestService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(private readonly TeamRequestService $service) {}

    // ── Show — student's own team ──────────────────────────────────────────────

    public function show(Request $request)
    {
        $student    = auth('student')->user();
        $activeYear = AcademicYear::active();

        // Find the team this student belongs to in the active year
        $team = null;
        $isLeader = false;
        $availableProjects = collect();

        if ($activeYear) {
            $team = Team::whereHas('students', fn ($q) =>
                            $q->where('students.id', $student->id)
                              ->where('team_student.academic_year_id', $activeYear->id)
                        )
                        ->where('academic_year_id', $activeYear->id)
                        ->with(['leader', 'students'])
                        ->first();

            if ($team) {
                $isLeader = $student->id === $team->leader_id;

                // Load current project assignment (if any)
                $team->currentProject = \DB::table('team_project')
                    ->where('team_id', $team->id)
                    ->join('project_ideas', 'project_ideas.id', '=', 'team_project.project_idea_id')
                    ->select('project_ideas.*')
                    ->first();

                // Leader can pick from available ideas in their level + year
                if ($isLeader) {
                    $availableProjects = ProjectIdea::where('level_id', $team->level_id)
                        ->where('academic_year_id', $activeYear->id)
                        ->get();
                }
            }
        }

        return view('student.team', compact('student', 'activeYear', 'team', 'isLeader', 'availableProjects'));
    }

    // ── Submit Request — leader only ───────────────────────────────────────────

    public function submitRequest(StoreTeamRequestRequest $request)
    {
        $student    = auth('student')->user();
        $activeYear = AcademicYear::active();

        if (! $activeYear) {
            abort(403, __('cms.academic_years.no_active_year'));
        }

        $team = Team::whereHas('students', fn ($q) =>
                        $q->where('students.id', $student->id)
                          ->where('team_student.academic_year_id', $activeYear->id)
                    )
                    ->where('academic_year_id', $activeYear->id)
                    ->firstOrFail();

        $this->service->createRequest($team, $student, $request->validated());

        return redirect()
            ->route('student.team.show')
            ->with('success', __('cms.teams.request_submitted'));
    }
}
