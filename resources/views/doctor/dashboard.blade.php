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
            <div class="cms-empty-state">
                <i class="bi bi-layers"></i>
                <p>{{ __('cms.doctor.not_assigned_yet') }}</p>
            </div>
        @else
            <div class="d-flex flex-wrap gap-3">
                @foreach($assignments as $assignment)
                    <div class="cms-level-card" style="display:flex;flex-direction:column;gap:.5rem;padding:.75rem 1rem;background:var(--cms-bg-card,#1a1a2e);border:1px solid var(--cms-border,rgba(255,255,255,.08));border-radius:.75rem;min-width:200px;">
                        <span class="cms-level-pill" style="margin:0;">
                            <i class="bi bi-bookmark-fill me-1" style="font-size:.75rem;"></i>
                            {{ $assignment->level->name }}
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('doctor.students.index', $assignment->level->id) }}"
                               class="cms-btn cms-btn-secondary cms-btn-sm" style="font-size:.75rem;">
                                <i class="bi bi-people me-1"></i>{{ __('cms.doctor.view_students') }}
                            </a>
                            <a href="{{ route('doctor.ideas.index', $assignment->level->id) }}"
                               class="cms-btn cms-btn-secondary cms-btn-sm" style="font-size:.75rem;">
                                <i class="bi bi-lightbulb me-1"></i>{{ __('cms.doctor.view_ideas') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
