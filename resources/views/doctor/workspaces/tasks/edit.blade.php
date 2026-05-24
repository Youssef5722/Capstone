@extends('layouts.app')

@section('title', __('cms.tasks.edit_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}">{{ $workspace->team->name ?? __('cms.teams.unnamed') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('doctor.tasks.show', [$level, $workspace, $task]) }}">{{ $task->title }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.tasks.edit_title') }}</span>
        </div>
        <h1>{{ __('cms.tasks.edit_title') }}</h1>
    </div>
</div>

<div class="cms-card" style="max-width:680px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-check2-square me-2" style="color:#a78bfa;"></i>{{ __('cms.tasks.edit_title') }}</h3>
    </div>
    <div class="cms-card-body">
        <form method="POST" action="{{ route('doctor.tasks.update', [$level, $workspace, $task]) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" for="task_title">{{ __('cms.tasks.name') }}</label>
                <input type="text" id="task_title" name="title"
                       value="{{ old('title', $task->title) }}"
                       class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="task_desc">{{ __('cms.tasks.description') }}</label>
                <textarea id="task_desc" name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="task_priority">{{ __('cms.tasks.priority') }}</label>
                    <select id="task_priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        @foreach(['low','medium','high'] as $p)
                            <option value="{{ $p }}" {{ old('priority',$task->priority) === $p ? 'selected' : '' }}>
                                {{ __('cms.tasks.priority_' . $p) }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="task_status">{{ __('cms.tasks.status') }}</label>
                    <select id="task_status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['pending','in_progress','submitted','approved','rejected'] as $st)
                            <option value="{{ $st }}" {{ old('status',$task->status) === $st ? 'selected' : '' }}>
                                {{ __('cms.tasks.status_' . $st) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="task_deadline">{{ __('cms.tasks.deadline') }}</label>
                    <input type="date" id="task_deadline" name="deadline"
                           value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}"
                           class="form-control @error('deadline') is-invalid @enderror">
                    @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="cms-btn cms-btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                </button>
                <a href="{{ route('doctor.tasks.show', [$level, $workspace, $task]) }}" class="cms-btn cms-btn-secondary">
                    <i class="bi bi-x-lg"></i> {{ __('cms.general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
