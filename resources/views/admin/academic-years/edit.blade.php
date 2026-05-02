@extends('layouts.app')

@section('title', __('cms.academic_years.edit_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('admin.academic-years.index') }}">{{ __('cms.academic_years.title') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.academic_years.edit_title') }}</span>
        </div>
        <h1>{{ __('cms.academic_years.edit_title') }}</h1>
        <p>{{ $academicYear->name }}</p>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-6">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-calendar-week me-2" style="color:#fbbf24;"></i>{{ __('cms.academic_years.edit_title') }}</h3>
            </div>
            <div class="cms-card-body">
                <form action="{{ route('admin.academic-years.update', $academicYear) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="cms-form-group">
                        <label class="cms-form-label" for="name">{{ __('cms.academic_years.name') }}</label>
                        <input type="text" class="cms-form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $academicYear->name) }}"
                               required>
                        <div class="cms-invalid-feedback">{{ $errors->first('name') }}</div>
                    </div>

                    <div class="cms-form-group">
                        <label class="cms-form-label" for="start_date">{{ __('cms.academic_years.start_date') }}</label>
                        <input type="date" class="cms-form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date"
                               value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}"
                               required>
                        <div class="cms-invalid-feedback">{{ $errors->first('start_date') }}</div>
                    </div>

                    <div class="cms-form-group">
                        <label class="cms-form-label" for="end_date">{{ __('cms.academic_years.end_date') }}</label>
                        <input type="date" class="cms-form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date"
                               value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}"
                               required>
                        <div class="cms-invalid-feedback">{{ $errors->first('end_date') }}</div>
                    </div>

                    <div class="d-flex gap-3 mt-2">
                        <button type="submit" class="cms-btn cms-btn-primary">
                            <i class="bi bi-check-lg"></i> {{ __('cms.academic_years.update') }}
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
