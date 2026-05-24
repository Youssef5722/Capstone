@extends('layouts.app')

@section('title', __('cms.student.dashboard_title') . ' — CMS')

@push('styles')
<style>
/* ── Student Dashboard Specific Styles ── */

/* Hero Banner */
.stu-hero {
    position: relative;
    background: linear-gradient(135deg,
        rgba(10, 255, 255, 0.15) 0%,
        rgba(122, 34, 253, 0.08) 50%,
        rgba(10, 255, 255, 0.05) 100%);
    border: 1px solid rgba(10, 255, 255, 0.25);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    flex-wrap: wrap;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stu-hero::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(10, 255, 255, 0.15) 0%, transparent 70%);
    pointer-events: none;
}

.stu-hero::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: 20%;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(122, 34, 253, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.stu-hero-content {
    position: relative;
    z-index: 1;
}

.stu-hero-greeting {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #0AFFFF;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stu-hero h1 {
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.stu-hero-sub {
    font-size: 0.95rem;
    color: var(--text-muted);
    max-width: 500px;
    margin: 0;
}

.stu-hero-meta {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

/* Info Card */
.stu-info-card {
    background: rgba(22, 27, 40, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 1.5rem;
    height: 100%;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.03);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: rgba(10, 255, 255, 0.05);
    border-color: rgba(10, 255, 255, 0.15);
    transform: translateX(4px);
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(10, 255, 255, 0.1);
    color: #0AFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-label {
    font-size: 0.75rem;
    color: var(--text-faint);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.info-value {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-top: 0.15rem;
}

.info-value.mono {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    letter-spacing: 1px;
}

/* Action Cards */
.stu-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 1.25rem;
    padding: 2.5rem 2rem;
    border-radius: 20px;
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    height: 100%;
    text-decoration: none;
}

.stu-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    opacity: 0;
    transition: opacity 0.3s;
}

.stu-action-card.cyan::before { background: linear-gradient(90deg, #0AFFFF, #38bdf8); }
.stu-action-card.purple::before { background: linear-gradient(90deg, #7A22FD, #a78bfa); }

.stu-action-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.stu-action-card.cyan:hover { border-color: rgba(10, 255, 255, 0.3); }
.stu-action-card.purple:hover { border-color: rgba(122, 34, 253, 0.3); }

.stu-action-card:hover::before {
    opacity: 1;
}

.stu-action-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    position: relative;
    z-index: 1;
    transition: transform 0.3s ease;
}

.stu-action-card.cyan .stu-action-icon {
    background: rgba(10, 255, 255, 0.1);
    color: #0AFFFF;
    box-shadow: 0 0 20px rgba(10, 255, 255, 0.15);
}

.stu-action-card.purple .stu-action-icon {
    background: rgba(122, 34, 253, 0.12);
    color: #a78bfa;
    box-shadow: 0 0 20px rgba(122, 34, 253, 0.15);
}

.stu-action-card:hover .stu-action-icon {
    transform: scale(1.1);
}

.stu-action-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    position: relative;
    z-index: 1;
}

.stu-action-desc {
    font-size: 0.85rem;
    color: var(--text-muted);
    line-height: 1.5;
    position: relative;
    z-index: 1;
}

.stu-action-btn {
    position: relative;
    z-index: 1;
    width: 100%;
    margin-top: 0.5rem;
}

[dir="rtl"] .stu-hero-meta {
    align-items: flex-start;
}

@media (max-width: 768px) {
    .stu-hero {
        padding: 1.5rem;
        flex-direction: column;
        align-items: flex-start;
    }
    .stu-hero h1 { font-size: 1.8rem; }
    .stu-hero-meta { align-items: flex-start; }
}
</style>
@endpush

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
            <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
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
                            {{ __('cms.teams.my_team_title') }} <i class="bi bi-arrow-right ms-2"></i>
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
                            {{ __('cms.workspace.view_workspace') }} <i class="bi bi-arrow-right ms-2"></i>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
