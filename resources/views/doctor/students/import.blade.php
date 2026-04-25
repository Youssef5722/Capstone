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
            <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
            <a href="{{ route('doctor.students.index', $level->id) }}" style="color:inherit;text-decoration:none;">
                {{ __('cms.student.list_title') }}
            </a>
            <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
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

        {{-- Instructions --}}
        <div class="cms-alert cms-alert-info mb-4" style="align-items:flex-start;">
            <i class="bi bi-info-circle-fill" style="margin-top:.1rem;"></i>
            <div>
                <strong>Expected Excel columns:</strong><br>
                <code>name</code> (required) &bull;
                <code>university_id</code> (required)<br>
                <span style="font-size:.8rem;color:var(--cms-text-muted);">
                    Students will register their own email address when they activate their account.<br>
                    Maximum file size: 5 MB &bull; Formats: .xlsx, .xls
                </span>
            </div>
        </div>

        <form method="POST"
              action="{{ route('doctor.students.import.store', $level->id) }}"
              enctype="multipart/form-data"
              id="importForm">
            @csrf

            <div class="mb-4 position-relative">
                <label class="form-label" for="importFile">
                    {{ __('cms.student.import_title') }} <span style="color:var(--cms-danger);">*</span>
                </label>
                <input type="file"
                       class="form-control @error('file') is-invalid @enderror"
                       name="file"
                       id="importFile"
                       accept=".xlsx,.xls"
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
                    <i class="bi bi-arrow-left me-1"></i> {{ __('cms.general.back') }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function() {
    const btn = document.getElementById('importSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> {{ __('cms.general.loading') }}';
});
</script>
@endpush

@endsection
