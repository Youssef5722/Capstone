@extends('layouts.app')

@section('title', $subTask->title . ' — CMS')

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
            <a href="{{ route('student.workspace.tasks.show', $task->id) }}">{{ $task->title }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ $subTask->title }}</span>
        </div>
        <h1>{{ $subTask->title }}</h1>
        <div class="d-flex gap-2 mt-2 flex-wrap">
            <span class="cms-badge" style="{{ $statusStyle($subTask->status) }}">{{ __('cms.tasks.status_' . $subTask->status) }}</span>
            @if($subTask->deadline)
                <span class="cms-badge" style="background:rgba(167,139,250,.15);color:#a78bfa;">
                    <i class="bi bi-calendar3 me-1"></i>{{ $subTask->deadline->format('Y-m-d') }}
                </span>
            @endif
        </div>
    </div>
    <a href="{{ route('student.workspace.tasks.show', $task->id) }}" class="cms-btn cms-btn-secondary align-self-start">
        <i class="bi bi-arrow-left"></i> {{ __('cms.subtasks.back_to_task') }}
    </a>
</div>

<div class="row g-4">

    {{-- ── Left: Sub-task info + Upload ────────────────────────────────────── --}}
    <div class="col-lg-7">

        {{-- Sub-task Info --}}
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-diagram-2-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.subtasks.show_title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($subTask->description)
                    <p style="color:var(--cms-text-muted);">{{ $subTask->description }}</p>
                @endif
                <div style="font-size:.85rem;color:var(--cms-text-muted);">
                    <i class="bi bi-person-fill me-1"></i>{{ $subTask->assignee->name ?? '—' }}
                    · {{ __('cms.tasks.title') }}: {{ $task->title }}
                </div>
            </div>
        </div>

        {{-- Upload file (assigned member only) --}}
        @if($isAssignee && !in_array($subTask->status, ['approved','submitted']))
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-file-earmark-arrow-up-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.submissions.upload_title') }}</h3>
            </div>
            <div class="cms-card-body">
                <form method="POST" action="{{ route('student.workspace.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="submittable_type" value="subtask">
                    <input type="hidden" name="submittable_id"   value="{{ $subTask->id }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="subtask_file">{{ __('cms.submissions.file') }}</label>
                        <input type="file" id="subtask_file" name="file"
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

        {{-- Submissions history + leader review --}}
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-file-earmark-arrow-up me-2" style="color:#0AFFFF;"></i>{{ __('cms.submissions.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($subTask->submissions->isEmpty())
                    <p style="color:var(--cms-text-muted);">{{ __('cms.submissions.no_submissions') }}</p>
                @else
                    @foreach($subTask->submissions->sortByDesc('created_at') as $sub)
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

                        {{-- Leader review actions --}}
                        @if($isLeader && $sub->status === 'pending')
                        <div class="d-flex gap-2 mt-3">
                            <form method="POST" action="{{ route('student.workspace.subtasks.submissions.approve', [$task->id, $subTask->id, $sub->id]) }}">
                                @csrf
                                <button type="submit" class="cms-btn cms-btn-success" style="padding:.3rem .65rem;font-size:.82rem;">
                                    <i class="bi bi-check-lg"></i> {{ __('cms.submissions.approve_btn') }}
                                </button>
                            </form>
                            <button type="button" class="cms-btn cms-btn-danger" style="padding:.3rem .65rem;font-size:.82rem;"
                                    data-bs-toggle="collapse" data-bs-target="#sub-reject-{{ $sub->id }}">
                                <i class="bi bi-x-lg"></i> {{ __('cms.submissions.reject_btn') }}
                            </button>
                        </div>
                        <div class="collapse mt-2" id="sub-reject-{{ $sub->id }}">
                            <form method="POST" action="{{ route('student.workspace.subtasks.submissions.reject', [$task->id, $subTask->id, $sub->id]) }}">
                                @csrf
                                <textarea name="rejection_reason" rows="2" class="form-control mb-2"
                                          placeholder="{{ __('cms.submissions.rejection_reason_hint') }}"></textarea>
                                <button type="submit" class="cms-btn cms-btn-danger" style="padding:.3rem .65rem;font-size:.82rem;">
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

    {{-- ── Right: Comments ─────────────────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-chat-left-dots-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.comments.title') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($subTask->comments->isEmpty())
                    <p style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.comments.no_comments') }}</p>
                @else
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($subTask->comments->sortBy('created_at') as $comment)
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
                    <input type="hidden" name="commentable_type" value="subtask">
                    <input type="hidden" name="commentable_id"   value="{{ $subTask->id }}">
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
