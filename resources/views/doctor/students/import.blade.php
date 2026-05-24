@extends('layouts.app')

@section('title', __('cms.student.import_title') . ' — ' . $level->name . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('doctor.dashboard') }}" style="color:inherit;text-decoration:none;">
                <i class="bi bi-house-fill"></i>
            </a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size:.7rem;"></i>
            <a href="{{ route('doctor.students.index', $level->id) }}" style="color:inherit;text-decoration:none;">
                {{ __('cms.student.list_title') }}
            </a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size:.7rem;"></i>
            <span>{{ __('cms.student.import_title') }}</span>
        </div>
        <h1>{{ __('cms.student.import_title') }}</h1>
        <p>{{ __('cms.doctor.level_context', ['name' => $level->name]) }} &mdash; {{ $activeYear->name }}</p>
    </div>
</div>

{{-- ── Import Form ──────────────────────────────────────────────────────── --}}
<div class="cms-card" style="max-width:640px;">
    <div class="cms-card-header">
        <h3>
            <i class="bi bi-file-earmark-spreadsheet me-2" style="color:#a78bfa;"></i>
            {{ __('cms.student.import_title') }}
        </h3>
    </div>
    <div class="cms-card-body">

        {{-- Fix 4: warn if students already exist --}}
        @if($studentsExist)
        <div class="cms-alert cms-alert-danger mb-4" style="align-items:flex-start;">
            <i class="bi bi-x-octagon-fill" style="margin-top:.1rem;"></i>
            <div>
                <strong>{{ __('cms.students.import_blocked_existing') }}</strong><br>
                <span style="font-size:.85rem;">
                    <a href="{{ route('doctor.students.index', $level->id) }}" style="color:inherit;text-decoration:underline;">
                        {{ __('cms.student.list_title') }}
                    </a>
                </span>
            </div>
        </div>
        @endif

        {{-- Instructions --}}
        <div class="cms-alert cms-alert-info mb-4" style="align-items:flex-start;">
            <i class="bi bi-info-circle-fill" style="margin-top:.1rem;"></i>
            <div>
                <strong>{{ __('cms.ui.expected_columns') }}</strong><br>
                <code>{{ __('cms.ui.col_name') }}</code> {{ __('cms.ui.required') }} &bull;
                <code>{{ __('cms.ui.col_university_id') }}</code> {{ __('cms.ui.required') }}<br>
                <span style="font-size:.8rem;color:var(--cms-text-muted);">
                    Students will register their own email address when they activate their account.<br>
                    Maximum file size: 5 MB &bull; Formats: .xlsx, .xls
                </span>
            </div>
        </div>

        <form method="POST"
              action="{{ route('doctor.students.import.store', $level->id) }}"
              enctype="multipart/form-data"
              id="importForm"
              {{ $studentsExist ? 'onsubmit=return false;' : '' }}>
            @csrf

            {{-- Fix 1: Activation Deadline --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" for="activation_deadline">
                    {{ __('cms.student.activation_deadline') }} <span style="color:var(--cms-danger);">*</span>
                </label>
                <input type="date"
                       class="form-control @error('activation_deadline') is-invalid @enderror"
                       name="activation_deadline"
                       id="activation_deadline"
                       value="{{ old('activation_deadline') }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       required>
                <div class="form-text" style="color:var(--cms-text-muted);">
                    {{ __('cms.student.activation_deadline_hint') }}
                </div>
                @error('activation_deadline')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 position-relative">
                <label class="form-label" for="importFile">
                    {{ __('cms.student.import_title') }} <span style="color:var(--cms-danger);">*</span>
                </label>
                <input type="file"
                       class="form-control @error('file') is-invalid @enderror"
                       name="file"
                       id="importFile"
                       accept=".xlsx,.xls"
                       {{ $studentsExist ? 'disabled' : '' }}
                       required>
                @error('file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="cms-btn cms-btn-primary" id="importSubmitBtn">
                    <i class="bi bi-upload me-1"></i> {{ __('cms.student.import_btn') }}
                </button>
                <a href="{{ route('doctor.students.index', $level->id) }}" class="cms-btn cms-btn-secondary">
                    <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} me-1"></i> {{ __('cms.general.back') }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function() {
    if (!this.checkValidity()) return;
    const btn = document.getElementById('importSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> {{ __('cms.general.loading') }}';
});
</script>
@endpush

@endsection
