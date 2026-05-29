@extends('layouts.app')

@section('title', __('cms.student.dashboard_title') . ' — CMS')

@section('content')

@php
    $s = auth('student')->user();
@endphp

{{-- ── Hero Banner ──────────────────────────────────────────────────────────── --}}
<div class="stu-hero">
    <div class="stu-hero-content">
        <div class="stu-hero-greeting">
            <i class="bi bi-stars"></i>
            {{ __('cms.student.dashboard_title') }}
        </div>
        <h1>{{ __('cms.student.welcome', ['name' => $s->name]) }}</h1>
        <p class="stu-hero-sub">{{ __('cms.student.portal_intro') }}</p>
    </div>
    
    <div class="stu-hero-meta">
        <span class="cms-badge cms-badge-cyan px-3 py-2" style="font-size: 0.85rem;">
            <i class="bi bi-mortarboard-fill"></i> {{ __('Student') }}
        </span>
        <div class="cms-breadcrumb" style="justify-content: flex-end;">
            <i class="bi bi-house-fill"></i>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size:0.6rem;"></i>
            <span>{{ __('cms.student.dashboard_title') }}</span>
        </div>
    </div>
</div>

{{-- ── Main Content Grid ────────────────────────────────────────────────── --}}
<div class="row g-4 mb-4">
    
    {{-- Info Card --}}
    <div class="col-xl-5 col-lg-6">
        <div class="stu-info-card">
            <h3 class="mb-4 d-flex align-items-center gap-2" style="font-size: 1.1rem; color: var(--text-primary);">
                <i class="bi bi-person-badge text-info"></i> Personal Information
            </h3>
            
            <div class="d-flex flex-column gap-3">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-card-text"></i>
                    </div>
                    <div>
                        <div class="info-label">{{ __('cms.student.university_id') }}</div>
                        <div class="info-value mono">{{ $s->university_id }}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <div class="info-label">{{ __('cms.student.email') }}</div>
                        <div class="info-value">{{ $s->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="col-xl-7 col-lg-6">
        <div class="row g-4 h-100">
            {{-- My Team --}}
            <div class="col-md-6">
                <a href="{{ route('student.team.show') }}" class="stu-action-card cyan">
                    <div class="stu-action-icon">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <div class="stu-action-title">{{ __('cms.teams.my_team_title') }}</div>
                        <div class="stu-action-desc mt-2">{{ __('cms.teams.not_assigned_desc') }}</div>
                    </div>
                    <div class="stu-action-btn">
                        <span class="cms-btn cms-btn-secondary w-100 justify-content-center">
                            {{ __('cms.teams.my_team_title') }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-2"></i>
                        </span>
                    </div>
                </a>
            </div>

            {{-- Workspace --}}
            <div class="col-md-6">
                <a href="{{ route('student.workspace.show') }}" class="stu-action-card purple">
                    <div class="stu-action-icon">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <div>
                        <div class="stu-action-title">{{ __('cms.workspace.nav') }}</div>
                        <div class="stu-action-desc mt-2">{{ __('cms.workspace.not_created_yet') }}</div>
                    </div>
                    <div class="stu-action-btn">
                        <span class="cms-btn cms-btn-primary w-100 justify-content-center">
                            {{ __('cms.workspace.view_workspace') }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-2"></i>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
