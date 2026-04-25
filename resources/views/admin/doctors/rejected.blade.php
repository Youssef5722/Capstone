@extends('layouts.app')

@section('title', __('cms.doctors.rejected_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.doctors.rejected_title') }}</span>
        </div>
        <h1>{{ __('cms.doctors.rejected_title') }}</h1>
    </div>
</div>

<div class="cms-card">
    <div class="cms-card-header">
        <h3><i class="bi bi-person-x me-2" style="color:#ef4444;"></i>{{ __('cms.doctors.rejected_title') }}</h3>
        <span class="cms-badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 0.25rem 0.6rem; border-radius: 99px; font-size: 0.85rem;">{{ $doctors->count() }} {{ __('cms.doctors.rejected') }}</span>
    </div>

    @if($doctors->isEmpty())
        <div class="cms-empty-state">
            <i class="bi bi-check-circle"></i>
            <p>{{ __('cms.doctors.no_rejected') }}</p>
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
                                {{-- Restore to Pending --}}
                                <form method="POST" action="{{ route('admin.doctors.restore', $doctor) }}" onsubmit="return confirm('{{ __('cms.doctors.confirm_restore_pending') }}')">
                                    @csrf
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="cms-btn cms-btn-warning cms-btn-sm" style="background: #f59e0b; color: #fff; border:none;">
                                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('cms.doctors.restore_to_pending') }}
                                    </button>
                                </form>

                                {{-- Direct Approval --}}
                                <form method="POST" action="{{ route('admin.doctors.restore', $doctor) }}" onsubmit="return confirm('{{ __('cms.doctors.confirm_restore_approved') }}')">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="cms-btn cms-btn-success cms-btn-sm">
                                        <i class="bi bi-check-lg"></i> {{ __('cms.doctors.approve') }}
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
