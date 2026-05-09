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

        return view('doctor.teams.distribute', compact('level', 'activeYear', 'unassignedCount'));
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
}
