@extends('layouts.app')

@section('title', __('cms.teams.distribute_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('doctor.teams.index', $level) }}">{{ __('cms.teams.index_title') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.distribute_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.distribute_title') }}</h1>
        <p>{{ __('cms.teams.distribute_intro', ['level' => $level->name]) }}</p>
    </div>
</div>

{{-- ── Stats ────────────────────────────────────────────────────────────────── --}}
<div class="cms-card mb-4" style="max-width:480px;">
    <div class="cms-card-body d-flex align-items-center gap-4">
        <i class="bi bi-people-fill" style="font-size:2.5rem;color:#a78bfa;"></i>
        <div>
            <div style="font-size:2rem;font-weight:700;color:var(--cms-text);">{{ $unassignedCount }}</div>
            <div style="color:var(--cms-text-muted);">{{ __('cms.teams.unassigned_students') }}</div>
        </div>
    </div>
</div>

@if($unassignedCount === 0)
    <div class="cms-alert cms-alert-info">
        <i class="bi bi-info-circle-fill"></i>
        <div>{{ __('cms.teams.all_assigned') }}</div>
    </div>
@else
{{-- ── Distribution Form ───────────────────────────────────────────────────── --}}
<div class="cms-card" style="max-width:540px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-shuffle me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.distribute_title') }}</h3>
    </div>
    <div class="cms-card-body">
        <form method="POST" action="{{ route('doctor.teams.distribute.preview', $level) }}">
            @csrf

            {{-- Mode --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('cms.teams.mode') }} <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" id="mode_balanced"
                               value="balanced" {{ old('mode', 'balanced') === 'balanced' ? 'checked' : '' }}>
                        <label class="form-check-label" for="mode_balanced">
                            <span class="fw-semibold">{{ __('cms.teams.mode_balanced') }}</span>
                            <div class="form-text">{{ __('cms.teams.mode_balanced_hint') }}</div>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" id="mode_fixed"
                               value="fixed" {{ old('mode') === 'fixed' ? 'checked' : '' }}>
                        <label class="form-check-label" for="mode_fixed">
                            <span class="fw-semibold">{{ __('cms.teams.mode_fixed') }}</span>
                            <div class="form-text">{{ __('cms.teams.mode_fixed_hint') }}</div>
                        </label>
                    </div>
                </div>
                @error('mode')
                    <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Team Size --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" for="team_size">
                    {{ __('cms.teams.team_size') }} <span class="text-danger">*</span>
                </label>
                <input type="number" id="team_size" name="team_size"
                       value="{{ old('team_size', 4) }}" min="2" max="20"
                       class="form-control @error('team_size') is-invalid @enderror"
                       style="max-width:140px;">
                @error('team_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text" style="color:var(--cms-text-muted);">{{ __('cms.teams.team_size_hint') }}</div>
            </div>

            <button type="submit" class="cms-btn cms-btn-primary">
                <i class="bi bi-eye"></i> {{ __('cms.teams.preview_btn') }}
            </button>
        </form>
    </div>
</div>
@endif

@endsection
