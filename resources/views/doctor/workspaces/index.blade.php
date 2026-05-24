@extends('layouts.app')

@section('title', __('cms.workspace.index_title') . ' — CMS')

@push('styles')
<style>
.ws-card {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    padding: 1.5rem;
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-radius: 16px;
    height: 100%;
    transition: all 0.3s var(--ease-smooth);
    position: relative;
    overflow: hidden;
}

.ws-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, #a78bfa, #0AFFFF);
    opacity: 0;
    transition: opacity 0.3s;
}

.ws-card:hover {
    transform: translateY(-4px);
    border-color: rgba(122, 34, 253, 0.3);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
}

.ws-card:hover::before { opacity: 1; }

.ws-team-label {
    font-size: .72rem;
    color: var(--text-faint);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: .2rem;
}

.ws-team-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.ws-team-name i { color: #a78bfa; }

.ws-project {
    font-size: .82rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: .4rem;
}

.ws-project i { color: #fbbf24; font-size: .85rem; }

.ws-stats {
    display: flex;
    gap: 1.25rem;
    font-size: .82rem;
    color: var(--text-muted);
}

.ws-stats span {
    display: flex;
    align-items: center;
    gap: .35rem;
}

.ws-progress-label {
    display: flex;
    justify-content: space-between;
    font-size: .72rem;
    color: var(--text-faint);
    margin-bottom: .35rem;
}

.ws-progress-bar {
    background: rgba(255,255,255,.07);
    border-radius: 999px;
    height: 6px;
    overflow: hidden;
}

.ws-progress-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #a78bfa, #0AFFFF);
    transition: width .6s var(--ease-smooth);
}
</style>
@endpush

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
            <span>{{ $level->name }}</span>
            <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
            <span>{{ __('cms.workspace.index_title') }}</span>
        </div>
        <h1>{{ __('cms.workspace.index_title') }}</h1>
        <p>{{ __('cms.workspace.index_intro', ['level' => $level->name]) }}</p>
    </div>
</div>

{{-- ── Workspaces Grid ──────────────────────────────────────────────────── --}}
<div class="cms-card">
    <div class="cms-card-header">
        <h3>
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:#a78bfa;"></i>
            {{ __('cms.workspace.index_title') }}
            <span class="cms-badge cms-badge-purple ms-2" style="font-size:.72rem;">{{ $workspaces->count() }}</span>
        </h3>
    </div>
    <div class="cms-card-body">
        @if($workspaces->isEmpty())
            <div class="cms-empty-state">
                <i class="bi bi-grid-3x3-gap"></i>
                <p>{{ __('cms.workspace.no_workspaces') }}</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($workspaces as $ws)
                @php
                    $statusColor = match($ws->status) {
                        'active'    => 'cms-badge-success',
                        'completed' => 'cms-badge-purple',
                        default     => 'cms-badge-danger',
                    };
                    $statusLabel = __('cms.workspace.status_' . $ws->status);
                    $phasesCount = $ws->phases->count();
                    $tasksCount  = $ws->tasks->count();
                    $doneCount   = $ws->tasks->where('status','approved')->count();
                    $progress    = $tasksCount > 0 ? round($doneCount / $tasksCount * 100) : 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="ws-card">

                        {{-- Header --}}
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                                <div class="ws-team-label">{{ __('cms.workspace.team') }}</div>
                                <h4 class="ws-team-name">
                                    <i class="bi bi-diagram-3-fill"></i>
                                    {{ $ws->team->name ?? __('cms.teams.unnamed') }}
                                </h4>
                            </div>
                            <span class="cms-badge {{ $statusColor }}">{{ $statusLabel }}</span>
                        </div>

                        {{-- Project --}}
                        @if($ws->team->currentProject)
                            <div class="ws-project">
                                <i class="bi bi-lightbulb-fill"></i>
                                {{ Str::limit($ws->team->currentProject->title, 45) }}
                            </div>
                        @endif

                        {{-- Stats --}}
                        <div class="ws-stats">
                            <span><i class="bi bi-layers" style="color:#a78bfa;"></i> {{ $phasesCount }} {{ __('cms.workspace.phases_count') }}</span>
                            <span><i class="bi bi-check2-square" style="color:#34d399;"></i> {{ $doneCount }}/{{ $tasksCount }} {{ __('cms.workspace.tasks_count') }}</span>
                        </div>

                        {{-- Progress bar --}}
                        <div>
                            <div class="ws-progress-label">
                                <span>{{ __('cms.workspace.progress') }}</span>
                                <span style="font-weight:600; color:{{ $progress >= 100 ? '#34d399' : ($progress > 50 ? '#a78bfa' : 'var(--text-faint)') }};">{{ $progress }}%</span>
                            </div>
                            <div class="ws-progress-bar">
                                <div class="ws-progress-fill" style="width:{{ $progress }}%;"></div>
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="mt-auto pt-1">
                            <a href="{{ route('doctor.workspaces.show', [$level, $ws]) }}"
                               class="cms-btn cms-btn-primary w-100 justify-content-center">
                                {{ __('cms.workspace.view_workspace') }}
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
