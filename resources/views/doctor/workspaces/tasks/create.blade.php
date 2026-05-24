@extends('layouts.app')

@section('title', __('cms.tasks.create_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}">{{ $workspace->team->name ?? __('cms.teams.unnamed') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.tasks.create_title') }}</span>
        </div>
        <h1>{{ __('cms.tasks.create_title') }}</h1>
    </div>
</div>

<div class="cms-card" style="max-width:680px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-check2-square me-2" style="color:#a78bfa;"></i>{{ __('cms.tasks.create_title') }}</h3>
    </div>
    <div class="cms-card-body">

        {{-- Leader notice --}}
        <div class="cms-alert cms-alert-warning mb-3">
            <i class="bi bi-info-circle-fill"></i>
            <div>{{ __('cms.tasks.leader_auto_assigned') }}
                <strong>{{ $workspace->team->leader->name ?? '—' }}</strong>
            </div>
        </div>

        <form method="POST" action="{{ route('doctor.tasks.store', [$level, $workspace]) }}">
            @csrf

            {{-- Phase --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="task_phase">{{ __('cms.tasks.phase') }}</label>
                <select id="task_phase" name="phase_id" class="form-select @error('phase_id') is-invalid @enderror" required>
                    <option value="">{{ __('cms.tasks.select_phase') }}</option>
                    @foreach($phases as $phase)
                        <option value="{{ $phase->id }}" {{ old('phase_id') == $phase->id ? 'selected' : '' }}>
                            {{ $phase->order }}. {{ $phase->title }}
                        </option>
                    @endforeach
                </select>
                @error('phase_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Title --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="task_title">{{ __('cms.tasks.name') }}</label>
                <input type="text" id="task_title" name="title"
                       value="{{ old('title') }}"
                       class="form-control @error('title') is-invalid @enderror"
                       placeholder="{{ __('cms.tasks.name') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="task_desc">{{ __('cms.tasks.description') }}</label>
                <textarea id="task_desc" name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="{{ __('cms.tasks.description') }}">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Priority + Deadline --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="task_priority">{{ __('cms.tasks.priority') }}</label>
                    <select id="task_priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        @foreach(['low','medium','high'] as $p)
                            <option value="{{ $p }}" {{ old('priority','medium') === $p ? 'selected' : '' }}>
                                {{ __('cms.tasks.priority_' . $p) }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="task_deadline">{{ __('cms.tasks.deadline') }}</label>
                    <input type="date" id="task_deadline" name="deadline"
                           value="{{ old('deadline') }}"
                           class="form-control @error('deadline') is-invalid @enderror">
                    @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="cms-btn cms-btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                </button>
                <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}" class="cms-btn cms-btn-secondary">
                    <i class="bi bi-x-lg"></i> {{ __('cms.general.cancel') }}
                </a>
            </div>
        </form>

    </div>
</div>

@endsection
