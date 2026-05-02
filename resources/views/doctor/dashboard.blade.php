@extends('layouts.app')

@section('title', __('cms.doctor.dashboard_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <span>{{ __('cms.doctor.dashboard_title') }}</span>
        </div>
        <h1>{{ __('cms.doctor.welcome', ['name' => $doctor->name]) }}</h1>
        <p>{{ __('cms.doctor.dashboard_intro') }}</p>
    </div>
    @if($activeYear)
        <span class="cms-badge cms-badge-success align-self-start">
            <i class="bi bi-calendar3-fill"></i>
            {{ __('cms.doctor.active_year_badge', ['name' => $activeYear->name]) }}
        </span>
    @else
        <span class="cms-badge cms-badge-danger align-self-start">
            {{ __('cms.doctor.no_active_year_badge') }}
        </span>
    @endif
</div>

{{-- ── Assigned Levels ──────────────────────────────────────────────────── --}}
<div class="cms-card">
    <div class="cms-card-header">
        <h3>
            <i class="bi bi-layers-fill me-2" style="color:#a78bfa;"></i>
            {{ __('cms.doctor.my_levels') }}
        </h3>
    </div>
    <div class="cms-card-body">
        @if(!$activeYear)
            <div class="cms-alert cms-alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ __('cms.doctor.no_active_year') }}</div>
            </div>
        @elseif($assignments->isEmpty())
            <div class="cms-empty-state" style="padding:4rem 1rem; text-align:center;">
                <i class="bi bi-clipboard-x" style="font-size:3rem; color:var(--cms-text-muted); margin-bottom:1rem; display:block;"></i>
                <p style="color:var(--cms-text-muted); font-size:1.1rem; margin:0;">{{ __('cms.doctor.no_levels_assigned') }}</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($assignments as $assignment)
                    <div class="col-md-6 col-lg-4">
                        <div class="cms-level-card" style="display:flex;flex-direction:column;gap:1.25rem;padding:1.5rem;background:var(--cms-bg-card,#1a1a2e);border:1px solid var(--cms-border,rgba(255,255,255,.08));border-radius:1rem;height:100%;">
                            <h4 style="margin:0;font-size:1.25rem;color:var(--cms-text);">
                                <i class="bi bi-bookmark-fill me-2" style="color:#a78bfa;"></i>
                                {{ $assignment->level->name }}
                            </h4>
                            
                            <div class="d-flex flex-column gap-2" style="font-size:0.9rem;color:var(--cms-text-muted);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-people me-2"></i>Students ({{ $activeYear->name ?? '—' }})</span>
                                    <span class="fw-bold" style="color:var(--cms-text);">{{ isset($assignment->level->students) ? $assignment->level->students->where('academic_year_id', $activeYear?->id)->count() : 0 }}</span>
                                </div>
                                @if(isset($assignment->level->ideas))
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-lightbulb me-2"></i>Project Ideas</span>
                                    <span class="fw-bold" style="color:var(--cms-text);">{{ $assignment->level->ideas->where('academic_year_id', $activeYear?->id)->count() }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="mt-auto pt-2">
                                <a href="{{ route('doctor.students.index', $assignment->level->id) }}"
                                   class="cms-btn cms-btn-primary w-100 justify-content-center">
                                    Manage Level <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
