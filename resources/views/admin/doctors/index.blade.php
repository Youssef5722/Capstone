@extends('layouts.app')

@section('title', __('cms.doctors.approved_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.doctors.approved_title') }}</span>
        </div>
        <h1>{{ __('cms.doctors.approved_title') }}</h1>
    </div>
</div>

<div class="cms-card">
    <div class="cms-card-header">
        <h3><i class="bi bi-people-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.doctors.approved_title') }}</h3>
        <span class="cms-badge cms-badge-purple">{{ $doctors->count() }} {{ __('cms.doctors.approved') }}</span>
    </div>

    @if($doctors->isEmpty())
        <div class="cms-empty-state">
            <i class="bi bi-people"></i>
            <p>{{ __('cms.doctors.no_approved') }}</p>
        </div>
    @else
        <div class="cms-table-wrapper">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>{{ __('cms.doctors.name') }}</th>
                        <th>{{ __('cms.doctors.email') }}</th>
                        <th>{{ __('cms.doctors.national_id') }}</th>
                        <th>{{ __('cms.doctors.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $doctor)
                    <tr>
                        <td class="td-name">{{ $doctor->name }}</td>
                        <td>{{ $doctor->email }}</td>
                        <td class="td-mono">{{ $doctor->national_id }}</td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.doctors.assignments.show', $doctor->id) }}" 
                                   class="cms-btn cms-btn-ghost cms-btn-sm">
                                    <i class="bi bi-eye"></i> {{ __('cms.doctors.view_levels') }}
                                </a>
                                <a href="{{ route('admin.doctors.assign.form', $doctor->id) }}" 
                                   class="cms-btn cms-btn-primary cms-btn-sm">
                                    <i class="bi bi-pencil-fill"></i> {{ __('cms.doctors.assign_levels') }}
                                </a>
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
