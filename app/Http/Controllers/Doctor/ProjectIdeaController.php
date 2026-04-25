<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectIdeaRequest;
use App\Http\Requests\UpdateProjectIdeaRequest;
use App\Models\Level;
use App\Models\ProjectIdea;
use App\Services\ProjectIdeaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectIdeaController extends Controller
{
    public function __construct(private readonly ProjectIdeaService $service) {}

    // ── Helper: pull middleware-resolved objects ───────────────────────────────

    private function resolved(Request $request): array
    {
        return [
            $request->attributes->get('resolvedLevel'),
            $request->attributes->get('activeYear'),
        ];
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolved($request);

        $ideas = $this->service->getIdeas(Auth::id(), $level->id, $activeYear->id);

        return view('doctor.ideas.index', compact('level', 'activeYear', 'ideas'));
    }

    // ── Create ─────────────────────────────────────────────────────────────────

    public function create(Request $request, Level $level)
    {
        [$level, $activeYear] = $this->resolved($request);

        return view('doctor.ideas.create', compact('level', 'activeYear'));
    }

    // ── Store ──────────────────────────────────────────────────────────────────

    public function store(StoreProjectIdeaRequest $request, Level $level)
    {
        [$level, $activeYear] = $this->resolved($request);

        $this->service->store([
            'doctor_id'        => Auth::id(),
            'level_id'         => $level->id,
            'academic_year_id' => $activeYear->id,
            'title'            => $request->title,
            'description'      => $request->description,
        ]);

        return redirect()
            ->route('doctor.ideas.index', $level->id)
            ->with('success', __('cms.doctor.idea_created'));
    }

    // ── Edit ───────────────────────────────────────────────────────────────────

    public function edit(Request $request, Level $level, ProjectIdea $idea)
    {
        [$level, $activeYear] = $this->resolved($request);

        // Ownership check — doctors can only edit their own ideas
        if ($idea->doctor_id !== Auth::id()) {
            abort(403, __('cms.doctor.unauthorized'));
        }

        return view('doctor.ideas.edit', compact('level', 'activeYear', 'idea'));
    }

    // ── Update ─────────────────────────────────────────────────────────────────

    public function update(UpdateProjectIdeaRequest $request, Level $level, ProjectIdea $idea)
    {
        [$level, $activeYear] = $this->resolved($request);

        if ($idea->doctor_id !== Auth::id()) {
            abort(403, __('cms.doctor.unauthorized'));
        }

        $this->service->update($idea, [
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('doctor.ideas.index', $level->id)
            ->with('success', __('cms.doctor.idea_updated'));
    }

    // ── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(Request $request, Level $level, ProjectIdea $idea)
    {
        [$level] = $this->resolved($request);

        if ($idea->doctor_id !== Auth::id()) {
            abort(403, __('cms.doctor.unauthorized'));
        }

        $this->service->delete($idea);

        return redirect()
            ->route('doctor.ideas.index', $level->id)
            ->with('success', __('cms.doctor.idea_deleted'));
    }
}
