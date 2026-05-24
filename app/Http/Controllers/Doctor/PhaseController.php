<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Phase;
use App\Models\Workspace;
use App\Services\PhaseService;
use Illuminate\Http\Request;

class PhaseController extends Controller
{
    public function __construct(private PhaseService $phaseService) {}

    public function create(Request $request, Level $level, Workspace $workspace)
    {
        return view('doctor.workspaces.phases.create', compact('level', 'workspace'));
    }

    public function store(Request $request, Level $level, Workspace $workspace)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:upcoming,active,completed',
            'order'       => 'required|integer|min:1|max:255',
        ]);

        $this->phaseService->store($workspace, $data);

        return redirect()
            ->route('doctor.workspaces.show', [$level, $workspace])
            ->with('success', __('cms.phases.created_success'));
    }

    public function edit(Request $request, Level $level, Workspace $workspace, Phase $phase)
    {
        return view('doctor.workspaces.phases.edit', compact('level', 'workspace', 'phase'));
    }

    public function update(Request $request, Level $level, Workspace $workspace, Phase $phase)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:upcoming,active,completed',
            'order'       => 'required|integer|min:1|max:255',
        ]);

        $this->phaseService->update($phase, $data);

        return redirect()
            ->route('doctor.workspaces.show', [$level, $workspace])
            ->with('success', __('cms.phases.updated_success'));
    }

    public function destroy(Request $request, Level $level, Workspace $workspace, Phase $phase)
    {
        $this->phaseService->delete($phase);

        return redirect()
            ->route('doctor.workspaces.show', [$level, $workspace])
            ->with('success', __('cms.phases.deleted_success'));
    }
}
