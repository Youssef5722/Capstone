@extends('layouts.app')

@section('title', __('cms.academic_years.add_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('admin.academic-years.index') }}">{{ __('cms.academic_years.title') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.academic_years.create') }}</span>
        </div>
        <h1>{{ __('cms.academic_years.add_title') }}</h1>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-6">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-calendar-plus me-2" style="color:#60a5fa;"></i>{{ __('cms.academic_years.add_title') }}</h3>
            </div>
            <div class="cms-card-body">
                <form action="{{ route('admin.academic-years.store') }}" method="POST">
                    @csrf

                    <div class="cms-form-group">
                        <label class="cms-form-label" for="name">{{ __('cms.academic_years.name') }}</label>
                        <input type="text" class="cms-form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}"
                               placeholder="{{ __('cms.ui.year_placeholder') }}" required>
                        <div class="cms-invalid-feedback">{{ $errors->first('name') }}</div>
                    </div>

                    <div class="cms-form-group">
                        <label class="cms-form-label" for="start_date">{{ __('cms.academic_years.start_date') }}</label>
                        <input type="date" class="cms-form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        <div class="cms-invalid-feedback">{{ $errors->first('start_date') }}</div>
                    </div>

                    <div class="cms-form-group">
                        <label class="cms-form-label" for="end_date">{{ __('cms.academic_years.end_date') }}</label>
                        <input type="date" class="cms-form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        <div class="cms-invalid-feedback">{{ $errors->first('end_date') }}</div>
                    </div>

                    <div class="d-flex gap-3 mt-2">
                        <button type="submit" class="cms-btn cms-btn-primary">
                            <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                        </button>
                        <a href="{{ route('admin.academic-years.index') }}" class="cms-btn cms-btn-ghost">
                            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                            {{ __('cms.general.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
