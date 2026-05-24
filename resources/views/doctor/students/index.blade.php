@extends('layouts.app')

@section('title', __('cms.student.list_title') . ' — ' . $level->name . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('doctor.dashboard') }}" style="color:inherit;text-decoration:none;">
                <i class="bi bi-house-fill"></i>
            </a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size:.7rem;"></i>
            <span>{{ __('cms.doctor.level_context', ['name' => $level->name]) }}</span>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size:.7rem;"></i>
            <span>{{ __('cms.student.list_title') }}</span>
        </div>
        <h1>{{ __('cms.student.list_title') }}</h1>
        <p>{{ __('cms.doctor.level_context', ['name' => $level->name]) }} &mdash; {{ $activeYear->name }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('doctor.students.import', $level->id) }}" class="cms-btn cms-btn-primary">
            <i class="bi bi-upload me-1"></i> {{ __('cms.student.import_btn') }}
        </a>
        <a href="{{ route('doctor.students.export', $level->id) }}" class="cms-btn cms-btn-secondary">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> {{ __('cms.student.export_btn') }}
        </a>
        {{-- Fix 6: Bulk Delete button --}}
        <button type="button" class="cms-btn cms-btn-danger" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            <i class="bi bi-trash-fill me-1"></i> {{ __('cms.students.bulk_delete_btn') }}
        </button>
    </div>
</div>

{{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
<div class="cms-card mb-3">
    <div class="cms-card-body" style="padding:.75rem 1.25rem;">
        {{-- Fix 11: Name search --}}
        <form method="GET" action="{{ route('doctor.students.index', $level->id) }}" class="mb-2" id="student-search-form">
            @if($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
            <div class="d-flex gap-2 align-items-center">
                <div class="input-group input-group-sm flex-grow-1" style="max-width:340px;">
                    <span class="input-group-text bg-transparent" style="border-right:none;">
                        <i class="bi bi-search" style="color:var(--text-faint);"></i>
                    </span>
                    <input type="text" name="search" id="student-search"
                           class="form-control border-start-0 ps-0"
                           placeholder="{{ __('cms.students.search_placeholder') }}"
                           value="{{ $search ?? '' }}"
                           autocomplete="off">
                    <button class="btn btn-primary" type="submit" style="padding:0 .75rem;">
                        {{ __('cms.general.search') }}
                    </button>
                </div>
                @if($search ?? null)
                    <a href="{{ route('doctor.students.index', array_filter(['level' => $level->id, 'filter' => $filter])) }}" class="cms-badge cms-badge-secondary" style="white-space:nowrap;">
                        <i class="bi bi-x me-1"></i>{{ __('cms.general.cancel') }}
                    </a>
                @endif
            </div>
        </form>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span style="font-size:.8rem;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                {{ __('cms.general.search') }}:
            </span>
            <a href="{{ route('doctor.students.index', $level->id) }}"
               class="cms-badge {{ !$filter ? 'cms-badge-primary' : 'cms-badge-secondary' }}">
               {{ __('cms.student.filter_all') }}
            </a>
            <a href="{{ route('doctor.students.index', [$level->id, 'filter' => 'activated']) }}"
               class="cms-badge {{ $filter === 'activated' ? 'cms-badge-success' : 'cms-badge-secondary' }}">
               <i class="bi bi-check-circle me-1"></i>{{ __('cms.student.filter_activated') }}
            </a>
            <a href="{{ route('doctor.students.index', [$level->id, 'filter' => 'not_activated']) }}"
               class="cms-badge {{ $filter === 'not_activated' ? 'cms-badge-danger' : 'cms-badge-secondary' }}">
               <i class="bi bi-hourglass me-1"></i>{{ __('cms.student.filter_not_activated') }}
            </a>
            <a href="{{ route('doctor.students.index', [$level->id, 'filter' => 'trashed']) }}"
               class="cms-badge {{ $filter === 'trashed' ? 'cms-badge-warning' : 'cms-badge-secondary' }}">
               <i class="bi bi-trash me-1"></i>{{ __('cms.student.filter_trashed') }}
            </a>
            <span class="ms-auto" style="font-size:.8rem;color:var(--text-muted);">
                {{ $students->total() }} {{ __('cms.student.name') }}(s)
            </span>
        </div>
    </div>
</div>

{{-- ── Student Table ────────────────────────────────────────────────────── --}}
<div class="cms-card">
    <div class="cms-card-body p-0">
        @if($students->isEmpty())
            <div class="cms-empty-state" style="padding:3rem;">
                <i class="bi bi-people"></i>
                <p>{{ __('cms.student.no_students') }}</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th style="width:3rem;">#</th>
                            <th>{{ __('cms.student.name') }}</th>
                            <th>{{ __('cms.student.university_id') }}</th>
                            <th>{{ __('cms.auth.activation_code') }}</th>
                            <th>{{ __('cms.doctors.status') }}</th>
                            <th style="width:8rem;">{{ __('cms.general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        <tr>
                            <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                            <td>
                                <span style="font-weight:600;">{{ $student->name }}</span>
                            </td>
                            <td>
                                <code style="font-size:.85rem;background:rgba(255,255,255,0.05);padding:.15rem .5rem;border-radius:.3rem;color:#a78bfa;">
                                    {{ $student->university_id }}
                                </code>
                            </td>
                            <td>
                                <code style="font-size:.85rem;letter-spacing:.1em;background:rgba(255,255,255,0.05);padding:.2rem .55rem;border-radius:.3rem;color:#0AFFFF;font-family:'Courier New',monospace;">
                                    {{ $student->activation_code ?? '—' }}
                                </code>
                            </td>
                            <td>
                                @if($student->is_active)
                                    <span class="cms-badge cms-badge-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>{{ __('cms.student.status_activated') }}
                                    </span>
                                @else
                                    <span class="cms-badge cms-badge-danger">
                                        <i class="bi bi-hourglass me-1"></i>{{ __('cms.student.status_pending') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(request('filter') === 'trashed')
                                <form method="POST" action="{{ route('doctor.students.restore', [$level, $student->id]) }}">
                                    @csrf
                                    <button type="submit" class="cms-btn cms-btn-warning cms-btn-sm">
                                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('cms.student.restore') }}
                                    </button>
                                </form>
                                @else
                                <form method="POST"
                                      action="{{ route('doctor.students.destroy', [$level->id, $student->id]) }}"
                                      onsubmit="return confirm({{ Js::from(__('cms.student.confirm_delete')) }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cms-btn cms-btn-danger cms-btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ── Pagination ─────────────────────────────────────────────────────────── --}}
@if($students->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $students->withQueryString()->links() }}
</div>
@endif

{{-- Fix 6: Bulk Delete Confirmation Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--cms-surface);border:1px solid var(--cms-border);border-radius:1rem;">
            <div class="modal-header" style="border-bottom:1px solid var(--cms-border);">
                <h5 class="modal-title" id="bulkDeleteModalLabel" style="color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('cms.students.bulk_delete_confirm_title') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="cms-alert cms-alert-danger mb-0">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <div>{{ __('cms.students.bulk_delete_confirm_body') }}</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--cms-border);">
                <button type="button" class="cms-btn cms-btn-secondary" data-bs-dismiss="modal">
                    {{ __('cms.students.transfer_cancel_btn') }}
                </button>
                <form method="POST" action="{{ route('doctor.students.bulk_destroy', $level->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="cms-btn cms-btn-danger">
                        <i class="bi bi-trash-fill me-1"></i>{{ __('cms.students.bulk_delete_confirm_btn') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Live search debounce removed per user feedback. Form submits natively via button/Enter.
</script>
@endpush

@endsection
