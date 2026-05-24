@extends('layouts.app')

@section('title', __('cms.workspace.show_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('doctor.workspaces.index', $level) }}">{{ __('cms.workspace.index_title') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ $workspace->team->name ?? __('cms.teams.unnamed') }}</span>
        </div>
        <h1>
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:#a78bfa;"></i>
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
        @php
            $statusColor = match($workspace->status) {
                'active'    => 'cms-badge-success',
                'completed' => 'cms-badge-info',
                default     => 'cms-badge-danger',
            };
        @endphp
        <span class="cms-badge {{ $statusColor }} align-self-center">
            {{ __('cms.workspace.status_' . $workspace->status) }}
        </span>
        <a href="{{ route('doctor.tasks.create', [$level, $workspace]) }}" class="cms-btn cms-btn-primary">
            <i class="bi bi-plus-lg"></i> {{ __('cms.tasks.add_task') }}
        </a>
        <a href="{{ route('doctor.phases.create', [$level, $workspace]) }}" class="cms-btn cms-btn-secondary">
            <i class="bi bi-layers"></i> {{ __('cms.phases.add_phase') }}
        </a>
    </div>
</div>

{{-- ── Stats Row ────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php $statCards = [
        ['label' => __('cms.workspace.total_tasks'),    'value' => $totalTasks,  'icon' => 'bi-check2-square',   'color' => '#a78bfa'],
        ['label' => __('cms.workspace.approved_tasks'), 'value' => $doneTasks,   'icon' => 'bi-check-circle',    'color' => '#22c55e'],
        ['label' => __('cms.workspace.pending_tasks'),  'value' => $pendingTasks,'icon' => 'bi-clock',           'color' => '#fbbf24'],
        ['label' => __('cms.workspace.phases_count'),   'value' => $workspace->phases->count(), 'icon' => 'bi-layers', 'color' => '#0AFFFF'],
    ]; @endphp
    @foreach($statCards as $card)
    <div class="col-6 col-md-3">
        <div class="cms-card" style="padding:1.25rem;text-align:center;">
            <i class="bi {{ $card['icon'] }}" style="font-size:1.75rem;color:{{ $card['color'] }};display:block;margin-bottom:.5rem;"></i>
            <div style="font-size:1.75rem;font-weight:700;color:var(--cms-text);">{{ $card['value'] }}</div>
            <div style="font-size:.8rem;color:var(--cms-text-muted);">{{ $card['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Tabs ─────────────────────────────────────────────────────────────── --}}
<ul class="nav nav-tabs mb-3" id="wsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-phases" data-bs-toggle="tab" data-bs-target="#pane-phases" type="button" role="tab">
            <i class="bi bi-layers me-1"></i>{{ __('cms.workspace.tab_phases') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-tasks" data-bs-toggle="tab" data-bs-target="#pane-tasks" type="button" role="tab">
            <i class="bi bi-check2-square me-1"></i>{{ __('cms.workspace.tab_tasks') }}
        </button>
    </li>
    {{-- Fix 9: Files Archive tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-files" data-bs-toggle="tab" data-bs-target="#pane-files" type="button" role="tab">
            <i class="bi bi-folder2-open me-1"></i>{{ __('cms.workspace.tab_files_archive') }}
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ── PHASES TAB ──────────────────────────────────────────────────────── --}}
    <div class="tab-pane fade show active" id="pane-phases" role="tabpanel">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-layers-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.phases.title') }}</h3>
                <a href="{{ route('doctor.phases.create', [$level, $workspace]) }}" class="cms-btn cms-btn-primary" style="padding:.35rem .9rem;font-size:.85rem;">
                    <i class="bi bi-plus-lg"></i> {{ __('cms.phases.add_phase') }}
                </a>
            </div>
            <div class="cms-card-body">
                @if($workspace->phases->isEmpty())
                    <div style="text-align:center;padding:3rem 1rem;">
                        <i class="bi bi-layers" style="font-size:2.5rem;color:var(--cms-text-muted);display:block;margin-bottom:.75rem;"></i>
                        <p style="color:var(--cms-text-muted);">{{ __('cms.phases.no_phases') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="cms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('cms.phases.name') }}</th>
                                    <th>{{ __('cms.phases.start_date') }}</th>
                                    <th>{{ __('cms.phases.end_date') }}</th>
                                    <th>{{ __('cms.phases.status') }}</th>
                                    <th>{{ __('cms.phases.tasks_count') }}</th>
                                    <th>{{ __('cms.general.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workspace->phases as $phase)
                                @php
                                    $phaseStatusColor = match($phase->status) {
                                        'active'    => 'cms-badge-success',
                                        'completed' => 'cms-badge-info',
                                        default     => '',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $phase->order }}</td>
                                    <td><span class="fw-semibold">{{ $phase->title }}</span></td>
                                    <td style="font-size:.85rem;color:var(--cms-text-muted);">{{ $phase->start_date->format('Y-m-d') }}</td>
                                    <td style="font-size:.85rem;color:var(--cms-text-muted);">{{ $phase->end_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="cms-badge {{ $phaseStatusColor }}" style="{{ $phaseStatusColor ? '' : 'background:rgba(251,191,36,.15);color:#fbbf24;' }}">
                                            {{ __('cms.phases.status_' . $phase->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="cms-badge cms-badge-info">{{ $phase->tasks->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('doctor.phases.edit', [$level, $workspace, $phase]) }}"
                                               class="cms-btn cms-btn-secondary" style="padding:.3rem .65rem;font-size:.8rem;">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('doctor.phases.destroy', [$level, $workspace, $phase]) }}"
                                                  onsubmit="return confirm('{{ __('cms.phases.confirm_delete') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="cms-btn cms-btn-danger" style="padding:.3rem .65rem;font-size:.8rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── TASKS TAB ───────────────────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="pane-tasks" role="tabpanel">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-check2-square me-2" style="color:#a78bfa;"></i>{{ __('cms.tasks.title') }}</h3>
                <a href="{{ route('doctor.tasks.create', [$level, $workspace]) }}" class="cms-btn cms-btn-primary" style="padding:.35rem .9rem;font-size:.85rem;">
                    <i class="bi bi-plus-lg"></i> {{ __('cms.tasks.add_task') }}
                </a>
            </div>
            <div class="cms-card-body">
                @if($workspace->tasks->isEmpty())
                    <div style="text-align:center;padding:3rem 1rem;">
                        <i class="bi bi-check2-square" style="font-size:2.5rem;color:var(--cms-text-muted);display:block;margin-bottom:.75rem;"></i>
                        <p style="color:var(--cms-text-muted);">{{ __('cms.tasks.no_tasks') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="cms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('cms.tasks.name') }}</th>
                                    <th>{{ __('cms.tasks.phase') }}</th>
                                    <th>{{ __('cms.tasks.priority') }}</th>
                                    <th>{{ __('cms.tasks.status') }}</th>
                                    <th>{{ __('cms.tasks.deadline') }}</th>
                                    <th>{{ __('cms.tasks.submissions_count') }}</th>
                                    <th>{{ __('cms.general.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workspace->tasks as $i => $task)
                                @php
                                    $prioColor = match($task->priority) {
                                        'high'   => 'cms-badge-danger',
                                        'medium' => '',
                                        default  => 'cms-badge-info',
                                    };
                                    $taskStatusColor = match($task->status) {
                                        'approved'    => 'cms-badge-success',
                                        'rejected'    => 'cms-badge-danger',
                                        'submitted'   => 'cms-badge-info',
                                        'in_progress' => '',
                                        default       => '',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><span class="fw-semibold">{{ $task->title }}</span></td>
                                    <td style="font-size:.85rem;color:var(--cms-text-muted);">{{ $task->phase->title ?? '—' }}</td>
                                    <td>
                                        <span class="cms-badge {{ $prioColor }}" style="{{ !$prioColor ? 'background:rgba(251,191,36,.15);color:#fbbf24;' : '' }}">
                                            {{ __('cms.tasks.priority_' . $task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="cms-badge {{ $taskStatusColor }}" style="{{ !$taskStatusColor ? 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);' : '' }}">
                                            {{ __('cms.tasks.status_' . str_replace('_', '_', $task->status)) }}
                                        </span>
                                    </td>
                                    <td style="font-size:.85rem;color:var(--cms-text-muted);">
                                        {{ $task->deadline?->format('Y-m-d') ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="cms-badge cms-badge-info">{{ $task->submissions->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('doctor.tasks.show', [$level, $workspace, $task]) }}"
                                               class="cms-btn cms-btn-secondary" style="padding:.3rem .65rem;font-size:.8rem;">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('doctor.tasks.destroy', [$level, $workspace, $task]) }}"
                                                  onsubmit="return confirm('{{ __('cms.tasks.confirm_delete') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="cms-btn cms-btn-danger" style="padding:.3rem .65rem;font-size:.8rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Fix 9: FILES ARCHIVE TAB --}}
    <div class="tab-pane fade" id="pane-files" role="tabpanel">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-folder2-open me-2" style="color:#a78bfa;"></i>{{ __('cms.workspace.files_archive_title') }}</h3>
            </div>
            <div class="cms-card-body">
                @php
                    $allSubs = collect();
                    foreach ($workspace->tasks as $t) {
                        foreach ($t->submissions as $s) {
                            $allSubs->push(['sub' => $s, 'context' => $t->title, 'type' => 'task', 'task' => $t]);
                        }
                        foreach ($t->subTasks as $st) {
                            foreach ($st->submissions as $s) {
                                $allSubs->push(['sub' => $s, 'context' => $st->title, 'type' => 'subtask', 'task' => $t]);
                            }
                        }
                    }
                @endphp
                @if($allSubs->isEmpty())
                    <p style="color:var(--cms-text-muted);">{{ __('cms.workspace.files_archive_empty') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="cms-table">
                            <thead>
                                <tr>
                                    <th>{{ __('cms.submissions.file_name') }}</th>
                                    <th>{{ __('cms.submissions.submitted_by') }}</th>
                                    <th>{{ __('cms.workspace.files_archive_task') }}</th>
                                    <th>{{ __('cms.submissions.status') }}</th>
                                    <th>{{ __('cms.submissions.submitted_at') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allSubs->sortByDesc(fn($i) => $i['sub']->created_at) as $item)
                                @php
                                    $sub = $item['sub'];
                                    $subStatusStyle = match($sub->status) {
                                        'approved'          => 'background:rgba(34,197,94,.15);color:#22c55e;',
                                        'rejected'          => 'background:rgba(239,68,68,.15);color:#ef4444;',
                                        'revision_required' => 'background:rgba(251,191,36,.15);color:#fbbf24;',
                                        default             => 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);',
                                    };
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $sub->file_name }}</td>
                                    <td style="font-size:.85rem;">{{ $sub->submitter->name ?? '—' }}</td>
                                    <td style="font-size:.85rem;color:var(--cms-text-muted);">{{ $item['context'] }}</td>
                                    <td><span class="cms-badge" style="{{ $subStatusStyle }}">{{ __('cms.submissions.status_' . str_replace('revision_required','revision',$sub->status)) }}</span></td>
                                    <td style="font-size:.8rem;color:var(--cms-text-muted);">{{ $sub->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('doctor.submissions.download', [$level, $workspace, $item['task'], $sub]) }}"
                                           class="cms-btn cms-btn-secondary" style="padding:.25rem .6rem;font-size:.8rem;">
                                            <i class="bi bi-download"></i> {{ __('cms.submissions.download_btn') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>{{-- end tab-content --}}

@endsection
