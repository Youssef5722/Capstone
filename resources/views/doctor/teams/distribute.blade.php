@extends('layouts.app')

@section('title', __('cms.teams.distribute_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('doctor.teams.index', $level) }}">{{ __('cms.teams.index_title') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.teams.distribute_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.distribute_title') }}</h1>
        <p>{{ __('cms.teams.distribute_intro', ['level' => $level->name]) }}</p>
    </div>
</div>

<div class="row gx-4">
    <div class="col-lg-4 mb-4">
        {{-- ── Stats ────────────────────────────────────────────────────────────────── --}}
        <div class="cms-card doc-stat-card purple h-100" style="border-radius: 16px; position: relative; overflow: hidden; transition: all 0.3s ease;">
            <div class="cms-card-body d-flex flex-column justify-content-center p-4">
                <div class="icon-wrap mb-3" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(122, 34, 253, 0.15); color: #a78bfa; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div style="font-size:2.5rem; font-weight:800; color:var(--text-primary); line-height: 1;">{{ $unassignedCount }}</div>
                    <div style="color:var(--text-muted); margin-top: 0.5rem; font-weight: 500;">{{ __('cms.teams.unassigned_students') }}</div>
                </div>
                {{-- Fix 3: Show activated vs total breakdown --}}
                <div class="mt-3 pt-3" style="border-top:1px solid rgba(167,139,250,.2);">
                    <div style="font-size:.8rem;color:var(--text-faint);">
                        <i class="bi bi-check-circle-fill me-1" style="color:#22c55e;"></i>
                        <strong style="color:#22c55e;">{{ $activatedStudents }}</strong> {{ __('cms.student.filter_activated') }}
                        &nbsp;/&nbsp;
                        <strong style="color:var(--text-muted);">{{ $totalStudents }}</strong> {{ __('cms.student.filter_all') }}
                    </div>
                    <div style="font-size:.75rem;color:var(--text-faint);margin-top:.3rem;">
                        <i class="bi bi-info-circle me-1"></i>{{ __('cms.teams.distribute_activated_note') }}
                    </div>
                </div>
            </div>
            <div class="card-bg-shape" style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%; background: radial-gradient(circle, rgba(122, 34, 253, 0.1) 0%, transparent 70%); pointer-events: none;"></div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        @if($unassignedCount === 0)
            <div class="cms-alert cms-alert-info d-flex align-items-center p-4 h-100" style="border-radius: 16px; border-width: 2px;">
                <i class="bi bi-info-circle-fill me-3" style="font-size: 1.5rem;"></i>
                <div style="font-size: 1.1rem; font-weight: 500;">{{ __('cms.teams.all_assigned') }}</div>
            </div>
        @else
        {{-- ── Distribution Form ───────────────────────────────────────────────────── --}}
        <div class="cms-card" style="border-radius: 16px;">
            <div class="cms-card-header" style="background: linear-gradient(135deg, rgba(10, 255, 255, 0.05), transparent); border-bottom: 1px solid var(--cms-border);">
                <h3 class="m-0 d-flex align-items-center gap-2" style="font-size: 1.25rem; font-weight: 700;">
                    <div class="icon-wrap" style="width:40px; height:40px; border-radius:10px; background:rgba(10, 255, 255, 0.12); color:#0AFFFF; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-shuffle"></i>
                    </div>
                    {{ __('cms.teams.distribute_title') }}
                </h3>
            </div>
            <div class="cms-card-body p-4 p-md-5">
                <form method="POST" action="{{ route('doctor.teams.distribute.preview', $level) }}">
                    @csrf

                    {{-- Mode --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-3" style="color: var(--text-primary); font-size: 1.05rem;">
                            {{ __('cms.teams.mode') }} <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <!-- Balanced Mode -->
                            <div class="flex-fill">
                                <input class="btn-check" type="radio" name="mode" id="mode_balanced"
                                       value="balanced" {{ old('mode', 'balanced') === 'balanced' ? 'checked' : '' }}>
                                <label class="mode-card w-100" for="mode_balanced">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="mode-icon"><i class="bi bi-symmetry-vertical"></i></div>
                                        <span class="fw-bold ms-2">{{ __('cms.teams.mode_balanced') }}</span>
                                        <div class="check-indicator ms-auto"><i class="bi bi-check-circle-fill"></i></div>
                                    </div>
                                    <div class="form-text mode-hint m-0">{{ __('cms.teams.mode_balanced_hint') }}</div>
                                </label>
                            </div>
                            
                            <!-- Fixed Mode -->
                            <div class="flex-fill">
                                <input class="btn-check" type="radio" name="mode" id="mode_fixed"
                                       value="fixed" {{ old('mode') === 'fixed' ? 'checked' : '' }}>
                                <label class="mode-card w-100" for="mode_fixed">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="mode-icon"><i class="bi bi-aspect-ratio"></i></div>
                                        <span class="fw-bold ms-2">{{ __('cms.teams.mode_fixed') }}</span>
                                        <div class="check-indicator ms-auto"><i class="bi bi-check-circle-fill"></i></div>
                                    </div>
                                    <div class="form-text mode-hint m-0">{{ __('cms.teams.mode_fixed_hint') }}</div>
                                </label>
                            </div>
                        </div>
                        @error('mode')
                            <div class="text-danger mt-2" style="font-size:.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Team Size --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold" for="team_size" style="color: var(--text-primary); font-size: 1.05rem;">
                            {{ __('cms.teams.team_size') }} <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="number" id="team_size" name="team_size"
                                   value="{{ old('team_size', 4) }}" min="2" max="20"
                                   class="cms-form-control @error('team_size') is-invalid @enderror"
                                   style="max-width:120px; font-size: 1.2rem; text-align: center; font-weight: 700;">
                            <div class="form-text m-0" style="color:var(--text-faint);">{{ __('cms.teams.team_size_hint') }}</div>
                        </div>
                        @error('team_size')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3 pt-3" style="border-top: 1px solid var(--cms-border);">
                        <button type="submit" class="cms-btn cms-btn-primary" style="padding: 0.75rem 2rem; font-weight: 600;">
                            <i class="bi bi-eye"></i> {{ __('cms.teams.preview_btn') }}
                        </button>
                        <a href="{{ route('doctor.teams.index', $level) }}" class="cms-btn cms-btn-ghost" style="padding: 0.75rem 2rem; font-weight: 600;">
                            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('cms.general.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Stat Card Hover */
.doc-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    border-color: rgba(122, 34, 253, 0.4);
}

/* Custom Mode Selection Cards */
.mode-card {
    display: block;
    padding: 1.25rem;
    border: 2px solid var(--cms-border);
    border-radius: 12px;
    background: var(--surface-card);
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.mode-card:hover {
    border-color: rgba(122, 34, 253, 0.4);
    background: rgba(122, 34, 253, 0.02);
}

.mode-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.mode-card span.fw-bold {
    color: var(--text-primary);
    font-size: 1.05rem;
}

.mode-hint {
    color: var(--text-faint);
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.check-indicator {
    color: #a78bfa;
    font-size: 1.2rem;
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Checked State */
.btn-check:checked + .mode-card {
    border-color: #7A22FD;
    background: linear-gradient(135deg, rgba(122, 34, 253, 0.08), transparent);
    box-shadow: 0 4px 15px rgba(122, 34, 253, 0.1);
}

.btn-check:checked + .mode-card .mode-icon {
    background: #7A22FD;
    color: #fff;
    box-shadow: 0 4px 10px rgba(122, 34, 253, 0.3);
}

.btn-check:checked + .mode-card .check-indicator {
    opacity: 1;
    transform: scale(1);
}

.btn-check:checked + .mode-card .mode-hint {
    color: var(--text-muted);
}
</style>
@endpush

@endsection
