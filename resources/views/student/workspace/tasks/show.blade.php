@extends('layouts.app')

@section('title', $task->title . ' — CMS')

@section('content')

@php
    $statusStyle = fn($s) => match($s) {
        'approved'    => 'background:rgba(34,197,94,.15);color:#22c55e;',
        'rejected'    => 'background:rgba(239,68,68,.15);color:#ef4444;',
        'submitted'   => 'background:rgba(10,255,255,.15);color:#0AFFFF;',
        'in_progress' => 'background:rgba(251,191,36,.15);color:#fbbf24;',
        default       => 'background:rgba(255,255,255,.08);color:var(--cms-text-muted);',
    };
@endphp

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('student.workspace.show') }}">{{ __('cms.workspace.show_title') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ $task->title }}</span>
        </div>
        <h1>{{ $task->title }}</h1>
        <div class="d-flex gap-2 mt-2 flex-wrap">
            <span class="cms-badge" style="{{ $statusStyle($task->status) }}">{{ __('cms.tasks.status_' . $task->status) }}</span>
            @if($task->deadline)
                <span class="cms-badge" style="background:rgba(167,139,250,.15);color:#a78bfa;">
                    <i class="bi bi-calendar3 me-1"></i>{{ $task->deadline->format('Y-m-d') }}
                </span>
            @endif
        </div>
    </div>
    <a href="{{ route('student.workspace.show') }}" class="cms-btn cms-btn-secondary align-self-start">
        <i class="bi bi-arrow-left"></i> {{ __('cms.general.back') }}
    </a>
</div>

<div class="row g-4">

    {{-- ── Left: Task info + Upload + Sub-Tasks (leader) ─────────────────── --}}
    <div class="col-lg-7">

        {{-- Task details --}}
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-info-circle-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.tasks.show_title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($task->description)
                    <p style="color:var(--cms-text-muted);font-size:.95rem;">{{ $task->description }}</p>
                @endif
                <div style="font-size:.85rem;color:var(--cms-text-muted);">
                    <i class="bi bi-layers me-1"></i>{{ $task->phase->title ?? '—' }}
                </div>
            </div>
        </div>

        {{-- Upload file (leader only for tasks) --}}
        @if($isAssignee && !in_array($task->status, ['approved','submitted']))
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-file-earmark-arrow-up-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.submissions.upload_title') }}</h3>
            </div>
            <div class="cms-card-body">
                <form method="POST" action="{{ route('student.workspace.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="submittable_type" value="task">
                    <input type="hidden" name="submittable_id"   value="{{ $task->id }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="task_file">{{ __('cms.submissions.file') }}</label>
                        <input type="file" id="task_file" name="file"
                               class="form-control @error('file') is-invalid @enderror" required>
                        <div class="form-text" style="color:var(--cms-text-muted);">{{ __('cms.submissions.file_hint') }}</div>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="cms-btn cms-btn-primary w-100 justify-content-center">
                        <i class="bi bi-cloud-upload"></i> {{ __('cms.submissions.submit_btn') }}
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Submissions history --}}
        @if($task->submissions->isNotEmpty())
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-clock-history me-2" style="color:#0AFFFF;"></i>{{ __('cms.submissions.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @foreach($task->submissions->sortByDesc('created_at') as $sub)
                <div style="padding:.85rem 1rem;border:1px solid var(--cms-border);border-radius:.5rem;margin-bottom:.75rem;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold" style="font-size:.9rem;">{{ $sub->file_name }}</div>
                            <div style="font-size:.78rem;color:var(--cms-text-muted);">{{ $sub->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <span class="cms-badge" style="{{ $statusStyle($sub->status) }}">
                            {{ __('cms.submissions.status_' . str_replace('revision_required','revision',$sub->status)) }}
                        </span>
                    </div>
                    @if($sub->rejection_reason)
                        <div style="font-size:.82rem;color:#ef4444;margin-top:.4rem;">
                            <i class="bi bi-x-circle me-1"></i>{{ $sub->rejection_reason }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Sub-Tasks (leader creates them) --}}
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-diagram-2-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.subtasks.title') }}</h3>
            </div>
            <div class="cms-card-body">

                {{-- Create sub-task (leader only) --}}
                @if($isLeader)
                <form method="POST" action="{{ route('student.workspace.subtasks.store', $task->id) }}" class="mb-4">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-md-5">
                            <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror"
                                   placeholder="{{ __('cms.subtasks.name') }}" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <select name="assigned_to" class="form-select form-select-sm @error('assigned_to') is-invalid @enderror" required>
                                <option value="">{{ __('cms.subtasks.select_member') }}</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="deadline" class="form-control form-control-sm"
                                   value="{{ old('deadline') }}">
                        </div>
                    </div>
                    <textarea name="description" rows="2" class="form-control form-control-sm mb-2"
                              placeholder="{{ __('cms.subtasks.description') }}">{{ old('description') }}</textarea>
                    <button type="submit" class="cms-btn cms-btn-primary" style="padding:.35rem .9rem;font-size:.85rem;">
                        <i class="bi bi-plus-lg"></i> {{ __('cms.subtasks.add_subtask') }}
                    </button>
                </form>
                @endif

                {{-- Sub-task list --}}
                @if($task->subTasks->isEmpty())
                    <p style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.subtasks.no_subtasks') }}</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($task->subTasks as $subTask)
                        <div style="padding:.75rem 1rem;border:1px solid var(--cms-border);border-radius:.5rem;display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;">
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem;">{{ $subTask->title }}</div>
                                <div style="font-size:.78rem;color:var(--cms-text-muted);">
                                    {{ $subTask->assignee->name ?? '—' }}
                                    @if($subTask->deadline)· {{ $subTask->deadline->format('Y-m-d') }}@endif
                                </div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="cms-badge" style="{{ $statusStyle($subTask->status) }}">
                                    {{ __('cms.tasks.status_' . $subTask->status) }}
                                </span>
                                <a href="{{ route('student.workspace.subtasks.show', [$task->id, $subTask->id]) }}"
                                   class="cms-btn cms-btn-secondary" style="padding:.3rem .6rem;font-size:.8rem;">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                @if($isLeader)
                                <form method="POST" action="{{ route('student.workspace.subtasks.destroy', [$task->id, $subTask->id]) }}"
                                      onsubmit="return confirm('{{ __('cms.subtasks.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cms-btn cms-btn-danger" style="padding:.3rem .6rem;font-size:.8rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Right: Comments ─────────────────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-chat-left-dots-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.comments.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($task->comments->isEmpty())
                    <p style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.comments.no_comments') }}</p>
                @else
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($task->comments->sortBy('created_at') as $comment)
                        @php $author = $comment->commentedBy; @endphp
                        <div style="padding:.75rem 1rem;border:1px solid var(--cms-border);border-radius:.5rem;">
                            <div style="font-size:.75rem;color:var(--cms-text-muted);margin-bottom:.3rem;">
                                <strong style="color:var(--cms-text);">{{ $author->name ?? '—' }}</strong>
                                · {{ $comment->created_at->format('Y-m-d H:i') }}
                            </div>
                            <div style="font-size:.9rem;">{{ $comment->comment }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('student.workspace.comments.store') }}">
                    @csrf
                    <input type="hidden" name="commentable_type" value="task">
                    <input type="hidden" name="commentable_id"   value="{{ $task->id }}">
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
