<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributeTeamsRequest;
use App\Models\Level;
use App\Services\TeamDistributionService;
use Illuminate\Http\Request;

class TeamDistributionController extends Controller
{
    public function __construct(private readonly TeamDistributionService $service) {}

    // ── Show Form ──────────────────────────────────────────────────────────────

    public function showForm(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $unassigned      = $this->service->getUnassigned($level, $activeYear);
        $unassignedCount = $unassigned->count();

        // Fix 3: show activated vs total for doctor awareness
        $totalStudents    = \App\Models\Student::where('level_id', $level->id)
                                               ->where('academic_year_id', $activeYear->id)
                                               ->count();
        $activatedStudents = \App\Models\Student::where('level_id', $level->id)
                                                ->where('academic_year_id', $activeYear->id)
                                                ->where('is_active', true)
                                                ->count();

        return view('doctor.teams.distribute', compact(
            'level', 'activeYear', 'unassignedCount', 'totalStudents', 'activatedStudents'
        ));
    }

    // ── Preview (session only — no DB writes) ──────────────────────────────────

    public function preview(DistributeTeamsRequest $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $mode     = $request->input('mode');
        $teamSize = (int) $request->input('team_size');

        $result = $this->service->preview($mode, $teamSize, $level, $activeYear);

        $groups    = $result['groups'];
        $remaining = $result['remaining'];

        return view('doctor.teams.preview', compact(
            'level', 'activeYear', 'groups', 'remaining', 'mode', 'teamSize'
        ));
    }

    // ── Confirm (persist from session) ────────────────────────────────────────

    public function confirm(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        // Retrieve stored group data from session
        $sessionGroups  = session('distribution.groups', []);
        $sessionLevelId = session('distribution.level_id');
        $sessionYearId  = session('distribution.year_id');

        // Validate the session matches the current level + year (prevent cross-level confirm)
        if ($sessionLevelId !== $level->id || $sessionYearId !== $activeYear->id) {
            return redirect()
                ->route('doctor.teams.distribute', $level)
                ->with('error', __('cms.teams.distribute_session_expired'));
        }

        if (empty($sessionGroups)) {
            return redirect()
                ->route('doctor.teams.distribute', $level)
                ->with('error', __('cms.teams.distribute_no_data'));
        }

        $this->service->confirm($sessionGroups, $level, $activeYear);

        return redirect()
            ->route('doctor.teams.index', $level)
            ->with('success', __('cms.teams.distribute_success'));
    }

    // Fix 7: adjust distribution preview (session mutation, no DB) ──────────────

    public function adjustPreview(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $request->validate([
            'student_id' => 'required|integer',
            'from_group' => 'required|integer|min:0',
            'to_group'   => 'required|integer|min:0',
        ]);

        $studentId = (int) $request->input('student_id');
        $fromIdx   = (int) $request->input('from_group');
        $toIdx     = (int) $request->input('to_group');

        // Validate session belongs to this level + year
        if (session('distribution.level_id') !== $level->id ||
            session('distribution.year_id')  !== $activeYear->id) {
            return redirect()
                ->route('doctor.teams.distribute', $level)
                ->with('error', __('cms.teams.distribute_session_expired'));
        }

        // Session stores IDs (int[]) not objects
        $groups = session('distribution.groups', []);

        if (!isset($groups[$fromIdx]) || !isset($groups[$toIdx]) || $fromIdx === $toIdx) {
            return back()->with('error', __('cms.teams.adjust_invalid'));
        }

        $key = array_search($studentId, $groups[$fromIdx]);
        if ($key === false) {
            return back()->with('error', __('cms.teams.adjust_invalid'));
        }

        // Move ID between groups
        array_splice($groups[$fromIdx], $key, 1);
        $groups[$toIdx][] = $studentId;

        session(['distribution.groups' => $groups]);

        // Re-hydrate Student models for the view (same pattern as confirm())
        $mode      = session('distribution.mode', 'balanced');
        $teamSize  = (int) session('distribution.team_size', 4);
        $remaining = session('distribution.remaining', []);

        $allIds  = array_merge(...array_values($groups));
        $allIds  = array_merge($allIds, $remaining);
        $students = \App\Models\Student::whereIn('id', $allIds)->get()->keyBy('id');

        $hydratedGroups    = array_map(fn ($ids) => array_map(fn ($id) => $students[$id], $ids), $groups);
        $hydratedRemaining = array_map(fn ($id) => $students[$id], $remaining);

        return view('doctor.teams.preview', [
            'level'     => $level,
            'activeYear'=> $activeYear,
            'groups'    => $hydratedGroups,
            'remaining' => $hydratedRemaining,
            'mode'      => $mode,
            'teamSize'  => $teamSize,
        ])->with('success', __('cms.teams.adjust_preview_success'));
    }
}
