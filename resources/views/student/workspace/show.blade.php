@extends('layouts.app')

@section('title', __('cms.workspace.show_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('student.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.workspace.show_title') }}</span>
        </div>
        <h1>
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:#0AFFFF;"></i>
            {{ $workspace->team->name ?? __('cms.teams.unnamed') }}
        </h1>
        @if($workspace->team->currentProject)
            <p style="color:var(--cms-text-muted);">
                <i class="bi bi-lightbulb-fill me-1" style="color:#fbbf24;"></i>
                {{ $workspace->team->currentProject->title }}
            </p>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap align-self-start">
        @if($isLeader)
            <span class="cms-badge cms-badge-success align-self-center">{{ __('cms.teams.you_are_leader') }}</span>
        @endif
        <span class="cms-badge cms-badge-cyan align-self-center">{{ __('cms.workspace.status_' . $workspace->status) }}</span>
    </div>
</div>

{{-- ── Stats Row ────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="cms-card" style="padding:1.25rem;text-align:center;">
            <i class="bi bi-check2-square" style="font-size:1.75rem;color:#a78bfa;display:block;margin-bottom:.5rem;"></i>
            <div style="font-size:1.75rem;font-weight:700;color:var(--cms-text);">{{ $myTasks->count() }}</div>
            <div style="font-size:.8rem;color:var(--cms-text-muted);">{{ __('cms.workspace.tab_my_tasks') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="cms-card" style="padding:1.25rem;text-align:center;">
            <i class="bi bi-diagram-2" style="font-size:1.75rem;color:#0AFFFF;display:block;margin-bottom:.5rem;"></i>
            <div style="font-size:1.75rem;font-weight:700;color:var(--cms-text);">{{ $mySubTasks->count() }}</div>
            <div style="font-size:.8rem;color:var(--cms-text-muted);">{{ __('cms.workspace.tab_my_subtasks') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="cms-card" style="padding:1.25rem;text-align:center;">
            <i class="bi bi-check-circle" style="font-size:1.75rem;color:#22c55e;display:block;margin-bottom:.5rem;"></i>
            <div style="font-size:1.75rem;font-weight:700;color:var(--cms-text);">{{ $myTasks->where('status','approved')->count() }}</div>
            <div style="font-size:.8rem;color:var(--cms-text-muted);">{{ __('cms.workspace.approved_tasks') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="cms-card" style="padding:1.25rem;text-align:center;">
            <i class="bi bi-layers" style="font-size:1.75rem;color:#fbbf24;display:block;margin-bottom:.5rem;"></i>
            <div style="font-size:1.75rem;font-weight:700;color:var(--cms-text);">{{ $workspace->phases->count() }}</div>
            <div style="font-size:.8rem;color:var(--cms-text-muted);">{{ __('cms.workspace.tab_phases') }}</div>
        </div>
    </div>
</div>

{{-- ── Tabs ─────────────────────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-3" id="stuWsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pane-my-tasks" type="button">
            <i class="bi bi-check2-square me-1"></i>{{ __('cms.workspace.tab_my_tasks') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pane-my-subtasks" type="button">
            <i class="bi bi-diagram-2 me-1"></i>{{ __('cms.workspace.tab_my_subtasks') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pane-phases" type="button">
            <i class="bi bi-layers me-1"></i>{{ __('cms.workspace.tab_phases') }}
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ── My Tasks ─────────────────────────────────────────────────────────── --}}
    <div class="tab-pane fade show active" id="pane-my-tasks">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-check2-square me-2" style="color:#0AFFFF;"></i>{{ __('cms.workspace.tab_my_tasks') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($myTasks->isEmpty())
                    <div style="text-align:center;padding:3rem 1rem;">
                        <i class="bi bi-check2-square" style="font-size:2.5rem;color:var(--cms-text-muted);display:block;margin-bottom:.75rem;"></i>
                        <p style="color:var(--cms-text-muted);">{{ __('cms.tasks.no_tasks') }}</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($myTasks as $task)
                        @php
                            $statusStyle = match($task->status) {
                                'approved'    => 'background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);',
                                'rejected'    => 'background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);',
                                'submitted'   => 'background:rgba(10,255,255,.08);border-color:rgba(10,255,255,.2);',
                                default       => '',
                            };
                            $statusBadge = match($task->status) {
                                'approved'    => 'background:rgba(34,197,94,.15);color:#22c55e;',
                                'rejected'    => 'background:rgba(239,68,68,.15);color:#ef4444;',
                                'submitted'   => 'background:rgba(10,255,255,.15);color:#0AFFFF;',
                                'in_progress' => 'background:rgba(251,191,36,.15);color:#fbbf24;',
                                default       => 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);',
                            };
                        @endphp
                        <div class="col-md-6">
                            <div style="padding:1.25rem;border:1px solid var(--cms-border);border-radius:.75rem;{{ $statusStyle }}height:100%;display:flex;flex-direction:column;gap:.75rem;">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h5 style="margin:0;font-size:1rem;">{{ $task->title }}</h5>
                                    <span class="cms-badge" style="{{ $statusBadge }}">{{ __('cms.tasks.status_' . $task->status) }}</span>
                                </div>
                                <div style="font-size:.8rem;color:var(--cms-text-muted);">
                                    <i class="bi bi-layers me-1"></i>{{ $task->phase->title ?? '—' }}
                                    @if($task->deadline)
                                        · <i class="bi bi-calendar3 me-1"></i>{{ $task->deadline->format('Y-m-d') }}
                                    @endif
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('student.workspace.tasks.show', $task->id) }}" class="cms-btn cms-btn-primary w-100 justify-content-center" style="font-size:.85rem;">
                                        {{ __('cms.general.actions') }} <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── My Sub-Tasks ─────────────────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="pane-my-subtasks">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-diagram-2 me-2" style="color:#0AFFFF;"></i>{{ __('cms.workspace.tab_my_subtasks') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($mySubTasks->isEmpty())
                    <div style="text-align:center;padding:3rem 1rem;">
                        <i class="bi bi-diagram-2" style="font-size:2.5rem;color:var(--cms-text-muted);display:block;margin-bottom:.75rem;"></i>
                        <p style="color:var(--cms-text-muted);">{{ __('cms.subtasks.no_subtasks') }}</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($mySubTasks as $subTask)
                        @php
                            $subStatusBadge = match($subTask->status) {
                                'approved'    => 'background:rgba(34,197,94,.15);color:#22c55e;',
                                'rejected'    => 'background:rgba(239,68,68,.15);color:#ef4444;',
                                'submitted'   => 'background:rgba(10,255,255,.15);color:#0AFFFF;',
                                default       => 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);',
                            };
                        @endphp
                        <div style="padding:.85rem 1rem;border:1px solid var(--cms-border);border-radius:.5rem;display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;">
                            <div>
                                <div class="fw-semibold" style="font-size:.95rem;">{{ $subTask->title }}</div>
                                <div style="font-size:.78rem;color:var(--cms-text-muted);">
                                    {{ __('cms.tasks.title') }}: {{ $subTask->task->title ?? '—' }}
                                    @if($subTask->deadline)
                                        · {{ $subTask->deadline->format('Y-m-d') }}
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="cms-badge" style="{{ $subStatusBadge }}">{{ __('cms.tasks.status_' . $subTask->status) }}</span>
                                <a href="{{ route('student.workspace.subtasks.show', [$subTask->task_id, $subTask->id]) }}"
                                   class="cms-btn cms-btn-secondary" style="padding:.3rem .65rem;font-size:.8rem;">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Phases Overview ──────────────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="pane-phases">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-layers-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.workspace.tab_phases') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($workspace->phases->isEmpty())
                    <p style="color:var(--cms-text-muted);">{{ __('cms.phases.no_phases') }}</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($workspace->phases as $phase)
                        @php
                            $phaseColor = match($phase->status) {
                                'active'    => '#22c55e',
                                'completed' => '#0AFFFF',
                                default     => '#64748b',
                            };
                        @endphp
                        <div style="padding:1rem 1.25rem;border:1px solid var(--cms-border);border-radius:.75rem;border-left:3px solid {{ $phaseColor }};">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span style="font-size:.75rem;color:var(--cms-text-muted);">{{ $phase->order }}.</span>
                                    <span class="fw-semibold ms-1">{{ $phase->title }}</span>
                                </div>
                                <span class="cms-badge" style="color:{{ $phaseColor }};background:rgba(255,255,255,.06);">
                                    {{ __('cms.phases.status_' . $phase->status) }}
                                </span>
                            </div>
                            <div style="font-size:.8rem;color:var(--cms-text-muted);margin-top:.4rem;">
                                {{ $phase->start_date->format('Y-m-d') }} → {{ $phase->end_date->format('Y-m-d') }}
                                · {{ $phase->tasks->count() }} {{ __('cms.phases.tasks_count') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
