@extends('layouts.app')

@section('title', __('cms.teams.create_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('doctor.teams.index', $level) }}">{{ __('cms.teams.index_title') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.create_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.create_title') }}</h1>
    </div>
</div>

{{-- ── Create Form ─────────────────────────────────────────────────────────── --}}
<div class="cms-card" style="max-width:700px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-diagram-3 me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.create_title') }}</h3>
    </div>
    <div class="cms-card-body">

        <form method="POST" action="{{ route('doctor.teams.store', $level) }}">
            @csrf

            {{-- Team Name --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" for="team_name">{{ __('cms.teams.name') }}</label>
                <input type="text" id="team_name" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="{{ __('cms.teams.name_placeholder') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text" style="color:var(--cms-text-muted);">{{ __('cms.teams.name_hint') }}</div>
            </div>

            {{-- Leader --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" for="leader_id">
                    {{ __('cms.teams.leader') }} <span class="text-danger">*</span>
                </label>
                @if($students->isEmpty())
                    <div class="cms-alert cms-alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ __('cms.teams.no_unassigned_students') }}</div>
                    </div>
                @else
                    <select id="leader_id" name="leader_id"
                            class="form-select @error('leader_id') is-invalid @enderror">
                        <option value="">{{ __('cms.teams.select_leader') }}</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('leader_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->university_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('leader_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endif
            </div>

            {{-- Members --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    {{ __('cms.teams.members') }} <span class="text-danger">*</span>
                </label>
                <div class="form-text mb-2" style="color:var(--cms-text-muted);">{{ __('cms.teams.members_hint') }}</div>
                @if($students->isEmpty())
                    <p style="color:var(--cms-text-muted);">{{ __('cms.teams.no_unassigned_students') }}</p>
                @else
                    <div style="max-height:250px;overflow-y:auto;border:1px solid var(--cms-border);border-radius:.5rem;padding:.75rem;background:var(--cms-bg-card);">
                        @foreach($students as $student)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox"
                                       name="student_ids[]" value="{{ $student->id }}"
                                       id="stu_{{ $student->id }}"
                                       {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="stu_{{ $student->id }}">
                                    {{ $student->name }}
                                    <span style="color:var(--cms-text-muted);font-size:.85rem;">({{ $student->university_id }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('student_ids')
                        <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                    @enderror
                @endif
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="cms-btn cms-btn-primary" {{ $students->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                </button>
                <a href="{{ route('doctor.teams.index', $level) }}" class="cms-btn cms-btn-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('cms.general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
