<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Team;
use App\Models\TeamRequest;
use App\Services\TeamRequestService;
use Illuminate\Http\Request;

class TeamRequestController extends Controller
{
    public function __construct(private readonly TeamRequestService $service) {}

    // ── Index — pending requests for this level ────────────────────────────────

    public function index(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        $requests = TeamRequest::whereHas('team', fn ($q) =>
                        $q->where('level_id', $level->id)
                          ->where('academic_year_id', $activeYear->id)
                    )
                    ->with(['team', 'requester', 'projectIdea'])
                    ->latest()
                    ->get();

        return view('doctor.requests.index', compact('level', 'activeYear', 'requests'));
    }

    // ── Approve ────────────────────────────────────────────────────────────────

    public function approve(Request $request, Level $level, TeamRequest $teamRequest)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        // Ensure the request belongs to a team in this level + year
        if ($teamRequest->team->level_id !== $level->id ||
            $teamRequest->team->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $this->service->approve($teamRequest, auth('web')->user());

        return redirect()
            ->route('doctor.requests.index', $level)
            ->with('success', __('cms.teams.request_approved'));
    }

    // ── Reject ─────────────────────────────────────────────────────────────────

    public function reject(Request $request, Level $level, TeamRequest $teamRequest)
    {
        [$level, $activeYear] = $this->resolveMiddlewareContext($request);

        if ($teamRequest->team->level_id !== $level->id ||
            $teamRequest->team->academic_year_id !== $activeYear->id) {
            abort(403);
        }

        $this->service->reject($teamRequest, auth('web')->user());

        return redirect()
            ->route('doctor.requests.index', $level)
            ->with('success', __('cms.teams.request_rejected'));
    }
}
