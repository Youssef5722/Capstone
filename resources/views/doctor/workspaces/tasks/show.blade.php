@extends('layouts.app')

@section('title', $task->title . ' — CMS')

@section('content')

@php
    $statusColorMap = [
        'pending'     => 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);',
        'in_progress' => 'background:rgba(251,191,36,.15);color:#fbbf24;',
        'submitted'   => 'background:rgba(10,255,255,.15);color:#0AFFFF;',
        'approved'    => 'background:rgba(34,197,94,.15);color:#22c55e;',
        'rejected'    => 'background:rgba(239,68,68,.15);color:#ef4444;',
    ];
    $prioColorMap = [
        'low'    => 'cms-badge-info',
        'medium' => '',
        'high'   => 'cms-badge-danger',
    ];
@endphp

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}">{{ $workspace->team->name ?? __('cms.teams.unnamed') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.tasks.show_title') }}</span>
        </div>
        <h1>{{ $task->title }}</h1>
        <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
            <span class="cms-badge" style="{{ $statusColorMap[$task->status] ?? '' }}">
                {{ __('cms.tasks.status_' . $task->status) }}
            </span>
            <span class="cms-badge {{ $prioColorMap[$task->priority] ?? '' }}" style="{{ !($prioColorMap[$task->priority] ?? '') ? 'background:rgba(251,191,36,.15);color:#fbbf24;' : '' }}">
                {{ __('cms.tasks.priority_' . $task->priority) }}
            </span>
            @if($task->deadline)
                <span class="cms-badge" style="background:rgba(167,139,250,.15);color:#a78bfa;">
                    <i class="bi bi-calendar3 me-1"></i>{{ $task->deadline->format('Y-m-d') }}
                </span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 align-self-start flex-wrap">
        <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}" class="cms-btn cms-btn-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('cms.workspace.back_to_index') }}
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- ── Left: Task Info + Submissions ─────────────────────────────────── --}}
    <div class="col-lg-7">

        {{-- Task Details --}}
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-info-circle-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.tasks.show_title') }}</h3>
            </div>
            <div class="cms-card-body">
                <div class="d-flex flex-column gap-3">
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <i class="bi bi-layers" style="color:var(--cms-text-muted);width:20px;text-align:center;"></i>
                        <div>
                            <div style="font-size:.7rem;color:var(--cms-text-muted);text-transform:uppercase;">{{ __('cms.tasks.phase') }}</div>
                            <div>{{ $task->phase->title ?? '—' }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <i class="bi bi-person-fill" style="color:var(--cms-text-muted);width:20px;text-align:center;"></i>
                        <div>
                            <div style="font-size:.7rem;color:var(--cms-text-muted);text-transform:uppercase;">{{ __('cms.tasks.assigned_to') }}</div>
                            <div>{{ $task->assignee->name ?? '—' }}</div>
                        </div>
                    </div>
                    @if($task->description)
                    <div style="padding:.75rem 1rem;background:rgba(255,255,255,.03);border-radius:.5rem;border:1px solid var(--cms-border);font-size:.9rem;color:var(--cms-text-muted);">
                        {{ $task->description }}
                    </div>
                    @endif
                </div>

                {{-- Update Status form --}}
                <form method="POST" action="{{ route('doctor.tasks.update', [$level, $workspace, $task]) }}" class="mt-4">
                    @csrf @method('PUT')
                    <input type="hidden" name="title"       value="{{ $task->title }}">
                    <input type="hidden" name="priority"    value="{{ $task->priority }}">
                    <input type="hidden" name="description" value="{{ $task->description }}">
                    <div class="d-flex gap-2 align-items-end">
                        <div style="flex:1;">
                            <label class="form-label fw-semibold" for="task_status_upd">{{ __('cms.tasks.update_status') }}</label>
                            <select id="task_status_upd" name="status" class="form-select">
                                @foreach(['pending','in_progress','submitted','approved','rejected'] as $st)
                                    <option value="{{ $st }}" {{ $task->status === $st ? 'selected' : '' }}>
                                        {{ __('cms.tasks.status_' . $st) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="cms-btn cms-btn-primary">{{ __('cms.general.save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Submissions --}}
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-file-earmark-arrow-up-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.submissions.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($task->submissions->isEmpty())
                    <p style="color:var(--cms-text-muted);">{{ __('cms.submissions.no_submissions') }}</p>
                @else
                    @foreach($task->submissions as $sub)
                    @php
                        $subStatusStyle = match($sub->status) {
                            'approved'         => 'background:rgba(34,197,94,.15);color:#22c55e;',
                            'rejected'         => 'background:rgba(239,68,68,.15);color:#ef4444;',
                            'revision_required'=> 'background:rgba(251,191,36,.15);color:#fbbf24;',
                            default            => 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);',
                        };
                    @endphp
                    <div style="padding:1rem;border:1px solid var(--cms-border);border-radius:.75rem;margin-bottom:.75rem;">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $sub->file_name }}</div>
                                <div style="font-size:.8rem;color:var(--cms-text-muted);">
                                    {{ __('cms.submissions.submitted_by') }}: {{ $sub->submitter->name ?? '—' }}
                                    · {{ $sub->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            <span class="cms-badge" style="{{ $subStatusStyle }}">{{ __('cms.submissions.status_' . str_replace('revision_required','revision',$sub->status)) }}</span>
                        </div>

                        @if($sub->rejection_reason)
                            <div class="cms-alert cms-alert-danger mt-2" style="padding:.5rem .75rem;">
                                <i class="bi bi-x-circle"></i>
                                <div style="font-size:.85rem;">{{ $sub->rejection_reason }}</div>
                            </div>
                        @endif

                        @if($sub->status === 'pending')
                        <div class="d-flex gap-2 mt-3">
                            <form method="POST" action="{{ route('doctor.submissions.approve', [$level, $workspace, $task, $sub]) }}">
                                @csrf
                                <button type="submit" class="cms-btn cms-btn-success" style="padding:.3rem .75rem;font-size:.85rem;">
                                    <i class="bi bi-check-lg"></i> {{ __('cms.submissions.approve_btn') }}
                                </button>
                            </form>
                            <button type="button" class="cms-btn cms-btn-danger" style="padding:.3rem .75rem;font-size:.85rem;"
                                    data-bs-toggle="collapse" data-bs-target="#reject-form-{{ $sub->id }}">
                                <i class="bi bi-x-lg"></i> {{ __('cms.submissions.reject_btn') }}
                            </button>
                        </div>
                        <div class="collapse mt-2" id="reject-form-{{ $sub->id }}">
                            <form method="POST" action="{{ route('doctor.submissions.reject', [$level, $workspace, $task, $sub]) }}">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="rejection_reason" rows="2" class="form-control"
                                              placeholder="{{ __('cms.submissions.rejection_reason_hint') }}"></textarea>
                                </div>
                                <button type="submit" class="cms-btn cms-btn-danger" style="padding:.3rem .75rem;font-size:.85rem;">
                                    {{ __('cms.submissions.reject_btn') }}
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

    {{-- ── Right: Sub-Tasks + Comments ────────────────────────────────────── --}}
    <div class="col-lg-5">

        {{-- Sub-Tasks --}}
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-diagram-2-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.subtasks.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($task->subTasks->isEmpty())
                    <p style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.subtasks.no_subtasks') }}</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($task->subTasks as $sub)
                        @php
                            $subStyle = match($sub->status) {
                                'approved'    => 'background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);',
                                'rejected'    => 'background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);',
                                'submitted'   => 'background:rgba(10,255,255,.08);border-color:rgba(10,255,255,.2);',
                                default       => '',
                            };
                        @endphp
                        <div style="padding:.75rem 1rem;border:1px solid var(--cms-border);border-radius:.5rem;{{ $subStyle }}">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <div>
                                    <div class="fw-semibold" style="font-size:.9rem;">{{ $sub->title }}</div>
                                    <div style="font-size:.78rem;color:var(--cms-text-muted);">
                                        {{ __('cms.subtasks.assigned_to') }}: {{ $sub->assignee->name ?? '—' }}
                                    </div>
                                </div>
                                <span class="cms-badge" style="{{ $statusColorMap[$sub->status] ?? '' }}">
                                    {{ __('cms.tasks.status_' . $sub->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Comments --}}
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-chat-left-dots-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.comments.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($task->comments->isEmpty())
                    <p style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.comments.no_comments') }}</p>
                @else
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($task->comments->sortBy('created_at') as $comment)
                        @php $author = $comment->commentedBy; @endphp
                        <div style="padding:.75rem 1rem;border:1px solid var(--cms-border);border-radius:.5rem;">
                            <div style="font-size:.75rem;color:var(--cms-text-muted);margin-bottom:.4rem;">
                                <strong style="color:var(--cms-text);">{{ $author->name ?? '—' }}</strong>
                                · {{ $comment->created_at->format('Y-m-d H:i') }}
                            </div>
                            <div style="font-size:.9rem;">{{ $comment->comment }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Post comment --}}
                <form method="POST" action="{{ route('doctor.task.comments.store', [$level, $workspace, $task]) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="comment" rows="3" class="form-control @error('comment') is-invalid @enderror"
                                  placeholder="{{ __('cms.comments.comment_hint') }}" required></textarea>
                        @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="cms-btn cms-btn-primary w-100 justify-content-center">
                        <i class="bi bi-send"></i> {{ __('cms.comments.post_btn') }}
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
