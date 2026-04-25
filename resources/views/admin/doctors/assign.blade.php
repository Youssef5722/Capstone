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
        <h1>{{ __('cms.assignments.assign_title', ['name' => $doctor->name]) }}</h1>
        @if($year)
            <p>{{ __('cms.assignments.active_year_current', ['name' => $year->name]) }}</p>
        @else
            <p style="color:#f87171;">{{ __('cms.assignments.no_active_year') }}</p>
        @endif
    </div>
    @if($year)
        <span class="cms-badge cms-badge-success align-self-start">
            <i class="bi bi-calendar3-fill"></i> {{ $year->name }}
        </span>
    @else
        <span class="cms-badge cms-badge-danger align-self-start">
            <i class="bi bi-exclamation-circle-fill"></i> {{ __('cms.assignments.no_active_year_badge') }}
        </span>
    @endif
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-7">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-layers-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.assignments.choose_levels') }}</h3>
            </div>
            <div class="cms-card-body">
                <form action="{{ route('admin.doctors.assign', $doctor->id) }}" method="POST">
                    @csrf

                    @forelse($levels as $level)
                    <label class="cms-checkbox-item">
                        <input type="checkbox" name="levels[]" value="{{ $level->id }}"
                            @if(in_array($level->id, $assignedLevelIds)) checked @endif>
                        <span class="cms-checkbox-label">{{ $level->name }}</span>
                    </label>
                    @empty
                    <div class="cms-alert cms-alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ __('cms.assignments.no_levels') }}</div>
                    </div>
                    @endforelse

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="cms-btn cms-btn-primary"
                            @if(!$year || $levels->isEmpty()) disabled @endif>
                            <i class="bi bi-check-lg"></i> {{ __('cms.assignments.save') }}
                        </button>
                        <a href="{{ route('admin.doctors.index') }}" class="cms-btn cms-btn-ghost">
                            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                            {{ __('cms.general.back') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
