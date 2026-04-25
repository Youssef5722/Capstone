@extends('layouts.app')

@section('title', __('cms.assignments.title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('admin.doctors.index') }}">{{ __('cms.doctors.approved_title') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.assignments.title') }}</span>
        </div>
        <h1>{{ __('cms.assignments.view_title', ['name' => $doctor->name]) }}</h1>
    </div>
    <a href="{{ route('admin.doctors.assign.form', $doctor->id) }}" class="cms-btn cms-btn-primary align-self-start">
        <i class="bi bi-pencil-fill"></i> {{ __('cms.assignments.edit_levels') }}
    </a>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3>
                    <i class="bi bi-diagram-3-fill me-2" style="color:#a78bfa;"></i>
                    {{ __('cms.assignments.assigned_levels') }}
                </h3>
                <!-- Academic Year badge -->
                @if($year)
                    <span class="cms-badge cms-badge-success">
                        <i class="bi bi-calendar3-fill"></i> {{ $year->name }}
                    </span>
                @else
                    <span class="cms-badge cms-badge-danger">
                        {{ __('cms.assignments.no_active_year_small') }}
                    </span>
                @endif
            </div>
            <div class="cms-card-body">
                @if($assignments->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($assignments as $assignment)
                            <span class="cms-level-pill">
                                <i class="bi bi-bookmark-fill me-1" style="font-size:.75rem;"></i>
                                {{ $assignment->level->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="cms-empty-state">
                        <i class="bi bi-layers"></i>
                        <p>{{ __('cms.assignments.no_levels_assigned') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
