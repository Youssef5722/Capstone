@extends('layouts.app')

@section('title', __('cms.admin.dashboard_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <span>{{ __('cms.admin.dashboard_title') }}</span>
        </div>
        <h1>{{ __('cms.admin.welcome', ['name' => auth('web')->user()->name]) }}</h1>
        <p>{{ __('cms.admin.dashboard_intro') }}</p>
    </div>
</div>

{{-- ── Quick Action Cards ───────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('admin.doctors.pending') }}" class="cms-action-card">
            <div class="action-icon" style="background:rgba(245,158,11,0.12);color:#fbbf24;">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <div class="action-label">{{ __('cms.admin.review_doctors') }}</div>
                <div class="action-sub">{{ __('cms.doctors.pending_title') }}</div>
            </div>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-arrow"></i>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('admin.academic-years.index') }}" class="cms-action-card">
            <div class="action-icon" style="background:rgba(59,130,246,0.12);color:#60a5fa;">
                <i class="bi bi-calendar3-fill"></i>
            </div>
            <div>
                <div class="action-label">{{ __('cms.admin.manage_years') }}</div>
                <div class="action-sub">{{ __('cms.academic_years.title') }}</div>
            </div>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-arrow"></i>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('admin.doctors.index') }}" class="cms-action-card">
            <div class="action-icon" style="background:rgba(122,34,253,0.12);color:#a78bfa;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="action-label">{{ __('cms.admin.manage_doctors_levels') }}</div>
                <div class="action-sub">{{ __('cms.doctors.approved_title') }}</div>
            </div>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-arrow"></i>
        </a>
    </div>
</div>

@endsection
