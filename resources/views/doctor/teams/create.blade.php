@extends('layouts.app')

@section('title', __('cms.teams.create_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <a href="{{ route('doctor.teams.index', $level) }}">{{ __('cms.teams.index_title') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.teams.create_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.create_title') }}</h1>
    </div>
</div>

{{-- ── Create Form ─────────────────────────────────────────────────────────── --}}
<div class="cms-card" style="max-width:700px; margin: 0 auto; border-radius: 16px;">
    <div class="cms-card-header" style="background: linear-gradient(135deg, rgba(122, 34, 253, 0.05), transparent); border-bottom: 1px solid var(--cms-border);">
        <h3 class="m-0 d-flex align-items-center gap-2" style="font-size: 1.25rem; font-weight: 700;">
            <div class="icon-wrap" style="width:40px; height:40px; border-radius:10px; background:rgba(122, 34, 253, 0.15); color:#a78bfa; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-diagram-3"></i>
            </div>
            {{ __('cms.teams.create_title') }}
        </h3>
    </div>
    <div class="cms-card-body p-4 p-md-5">

        <form method="POST" action="{{ route('doctor.teams.store', $level) }}">
            @csrf

            {{-- Team Name --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" for="team_name" style="color: var(--text-primary);">
                    {{ __('cms.teams.name') }}
                </label>
                <input type="text" id="team_name" name="name" value="{{ old('name') }}"
                       class="cms-form-control @error('name') is-invalid @enderror"
                       placeholder="{{ __('cms.teams.name_placeholder') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text" style="color:var(--text-faint); margin-top: 0.5rem; font-size: 0.85rem;">{{ __('cms.teams.name_hint') }}</div>
            </div>

            {{-- Leader --}}
            <div class="mb-4">
                <label class="form-label fw-semibold" for="leader_id" style="color: var(--text-primary);">
                    {{ __('cms.teams.leader') }} <span class="text-danger">*</span>
                </label>
                @if($students->isEmpty())
                    <div class="cms-alert cms-alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ __('cms.teams.no_unassigned_students') }}</div>
                    </div>
                @else
                    <select id="leader_id" name="leader_id"
                            class="cms-form-control @error('leader_id') is-invalid @enderror" style="appearance: auto; background-color: #0C101A; color: var(--text-primary);">
                        <option value="">{{ __('cms.teams.select_leader') }}</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('leader_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->university_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('leader_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endif
            </div>

            {{-- Members --}}
            <div class="mb-5">
                <label class="form-label fw-semibold" style="color: var(--text-primary);">
                    {{ __('cms.teams.members') }} <span class="text-danger">*</span>
                </label>
                <div class="form-text mb-3" style="color:var(--text-faint); font-size: 0.85rem;">{{ __('cms.teams.members_hint') }}</div>
                @if($students->isEmpty())
                    <p style="color:var(--text-muted);">{{ __('cms.teams.no_unassigned_students') }}</p>
                @else
                    {{-- Fix 12: search input --}}
                    <div class="mb-2 position-relative">
                        <i class="bi bi-search" style="position:absolute;top:50%;inset-inline-start:.75rem;transform:translateY(-50%);color:var(--text-faint);"></i>
                        <input type="text" id="member-search-create" class="form-control form-control-sm ps-4"
                               placeholder="{{ __('cms.students.search_placeholder') }}" autocomplete="off">
                    </div>
                    <div style="max-height:300px; overflow-y:auto; border:1px solid var(--cms-border); border-radius:12px; padding:1rem; background: rgba(0,0,0,0.02);" class="custom-scroll">
                        @foreach($students as $student)
                            <div class="form-check custom-checkbox mb-3 member-item-create" data-name="{{ $student->name }}">
                                <input class="form-check-input" type="checkbox"
                                       name="student_ids[]" value="{{ $student->id }}"
                                       id="stu_{{ $student->id }}"
                                       {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}
                                       style="cursor: pointer; width: 1.25rem; height: 1.25rem;">
                                <label class="form-check-label ms-2 d-flex align-items-center" for="stu_{{ $student->id }}" style="cursor: pointer;">
                                    <span class="fw-medium" style="color: var(--text-primary);">{{ $student->name }}</span>
                                    <span class="ms-2 badge" style="background: rgba(122,34,253,0.1); color: #a78bfa; font-size: 0.75rem;">{{ $student->university_id }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('student_ids')
                        <div class="text-danger mt-2" style="font-size:.875rem;">{{ $message }}</div>
                    @enderror
                @endif
            </div>

            <div class="d-flex gap-3 pt-3" style="border-top: 1px solid var(--cms-border);">
                <button type="submit" class="cms-btn cms-btn-primary" {{ $students->isEmpty() ? 'disabled' : '' }} style="padding: 0.75rem 2rem; font-weight: 600;">
                    <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                </button>
                <a href="{{ route('doctor.teams.index', $level) }}" class="cms-btn cms-btn-ghost" style="padding: 0.75rem 2rem; font-weight: 600;">
                    <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('cms.general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Custom Scrollbar for Members list */
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: var(--cms-border);
    border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #a78bfa;
}

/* Custom Checkbox Animation */
.custom-checkbox .form-check-input {
    transition: all 0.2s ease-in-out;
}
.custom-checkbox .form-check-input:checked {
    background-color: #7A22FD;
    border-color: #7A22FD;
    transform: scale(1.1);
}
</style>
@endpush

@push('scripts')
<script>
// Fix 12: leader dropdown → auto-check & lock the corresponding member checkbox
(function () {
    const leaderSelect   = document.getElementById('leader_id');
    const memberBoxes    = document.querySelectorAll('input[name="student_ids[]"]');
    const memberSearch   = document.getElementById('member-search-create');

    // Advanced Arabic Normalization (Handles presentation forms like ﯾ -> ي and Alef variations)
    function normalizeArabic(text) {
        if (!text) return '';
        return text.normalize('NFKC').toLowerCase().trim()
            .replace(/[أإآ]/g, 'ا')
            .replace(/ة/g, 'ه')
            .replace(/ى/g, 'ي')
            .replace(/ؤ/g, 'و')
            .replace(/ئ/g, 'ي');
    }

    function applyLeaderLock() {
        const leaderId = leaderSelect ? leaderSelect.value : null;
        memberBoxes.forEach(cb => {
            if (cb.value === leaderId) {
                cb.checked  = true;
                cb.disabled = true;
                cb.closest('.form-check').title = '{{ __("cms.teams.leader") }}';
            } else {
                cb.disabled = false;
            }
        });
    }

    if (leaderSelect) {
        leaderSelect.addEventListener('change', applyLeaderLock);
        applyLeaderLock(); // run on page load for old() value
    }

    // Live search filter for members list
    if (memberSearch) {
        memberSearch.addEventListener('input', function () {
            const q = normalizeArabic(this.value);
            const terms = q.split(/\s+/);
            document.querySelectorAll('.member-item-create').forEach(item => {
                const name = normalizeArabic(item.dataset.name);
                const matches = terms.every(t => name.includes(t));
                item.style.display = matches ? '' : 'none';
            });
        });
    }
})();
</script>
@endpush

@endsection
