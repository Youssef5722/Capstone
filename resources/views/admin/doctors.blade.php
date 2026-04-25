{{-- DEPRECATED: This view is superseded by admin/doctors/pending.blade.php --}}
@extends('layouts.app')

@section('title', __('cms.doctors.pending_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.doctors.pending_title') }}</span>
        </div>
        <h1>{{ __('cms.doctors.pending_title') }}</h1>
    </div>
</div>

<div class="cms-card">
    <div class="cms-card-header">
        <h3><i class="bi bi-person-clock me-2" style="color:#fbbf24;"></i>{{ __('cms.doctors.pending_title') }}</h3>
        <span class="cms-badge cms-badge-warning">{{ $doctors->count() }} {{ __('cms.doctors.pending') }}</span>
    </div>

    @if($doctors->isEmpty())
        <div class="cms-empty-state">
            <i class="bi bi-check-circle"></i>
            <p>{{ __('cms.doctors.no_pending') }}</p>
        </div>
    @else
        <div class="cms-table-wrapper">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>{{ __('cms.doctors.name') }}</th>
                        <th>{{ __('cms.doctors.email') }}</th>
                        <th>{{ __('cms.doctors.national_id') }}</th>
                        <th>{{ __('cms.doctors.requested_levels') }}</th>
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
                            @if(!empty($doctor->requested_levels))
                                @foreach($doctor->requested_levels as $level)
                                    <span class="badge bg-secondary me-1">{{ $level }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">{{ __('cms.doctors.no_preference') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <form action="{{ route('admin.doctors.approve', $doctor->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="cms-btn cms-btn-success cms-btn-sm"
                                        onclick="return confirm('{{ __('cms.doctors.confirm_approve') }}')">
                                        <i class="bi bi-check-lg"></i> {{ __('cms.doctors.approve') }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.doctors.reject', $doctor->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="cms-btn cms-btn-danger cms-btn-sm"
                                        onclick="return confirm('{{ __('cms.doctors.confirm_reject') }}')">
                                        <i class="bi bi-x-lg"></i> {{ __('cms.doctors.reject') }}
                                    </button>
                                </form>
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
