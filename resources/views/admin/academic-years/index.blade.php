@extends('layouts.app')

@section('title', __('cms.academic_years.manage_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.academic_years.manage_title') }}</span>
        </div>
        <h1>{{ __('cms.academic_years.manage_title') }}</h1>
    </div>
    <a href="{{ route('admin.academic-years.create') }}" class="cms-btn cms-btn-primary align-self-start">
        <i class="bi bi-plus-lg"></i> {{ __('cms.academic_years.create') }}
    </a>
</div>

<div class="cms-card">
    <div class="cms-card-header">
        <h3><i class="bi bi-calendar3 me-2" style="color:#60a5fa;"></i>{{ __('cms.academic_years.title') }}</h3>
        <span class="cms-badge cms-badge-muted">{{ $academicYears->count() }}</span>
    </div>

    @if($academicYears->isEmpty())
        <div class="cms-empty-state">
            <i class="bi bi-calendar-x"></i>
            <p>{{ __('cms.academic_years.no_years') }}</p>
        </div>
    @else
        <div class="cms-table-wrapper">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>{{ __('cms.academic_years.name') }}</th>
                        <th>{{ __('cms.academic_years.start_date') }}</th>
                        <th>{{ __('cms.academic_years.end_date') }}</th>
                        <th>{{ __('cms.academic_years.status') }}</th>
                        <th>{{ __('cms.general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($academicYears as $year)
                    <tr>
                        <td class="td-name">{{ $year->name }}</td>
                        <td>{{ $year->start_date->format('Y-m-d') }}</td>
                        <td>{{ $year->end_date->format('Y-m-d') }}</td>
                        <td>
                            @if($year->is_active)
                                <span class="cms-badge cms-badge-success">
                                    <i class="bi bi-circle-fill" style="font-size:.5rem;"></i>
                                    {{ __('cms.academic_years.active') }}
                                </span>
                            @else
                                <span class="cms-badge cms-badge-muted">
                                    {{ __('cms.academic_years.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.academic-years.edit', $year) }}" 
                                   class="cms-btn cms-btn-warning cms-btn-sm">
                                    <i class="bi bi-pencil"></i> {{ __('cms.academic_years.edit') }}
                                </a>
                                @if(!$year->is_active)
                                <form action="{{ route('admin.academic-years.activate', $year) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="cms-btn cms-btn-success cms-btn-sm"
                                        onclick="return confirm('{{ __('cms.academic_years.confirm_activate') }}')">
                                        <i class="bi bi-lightning-fill"></i> {{ __('cms.academic_years.activate') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
