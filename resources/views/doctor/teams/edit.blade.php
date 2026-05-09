@extends('layouts.app')

@section('title', __('cms.teams.edit_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('doctor.teams.index', $level) }}">{{ __('cms.teams.index_title') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.edit_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.edit_title') }}: {{ $team->name ?? __('cms.teams.unnamed') }}</h1>
    </div>
</div>

{{-- ── Update Name & Leader ─────────────────────────────────────────────────── --}}
<div class="cms-card mb-4" style="max-width:700px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-pencil-square me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.edit_details') }}</h3>
    </div>
    <div class="cms-card-body">
        <form method="POST" action="{{ route('doctor.teams.update', [$level, $team]) }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold" for="edit_name">{{ __('cms.teams.name') }}</label>
                <input type="text" id="edit_name" name="name"
                       value="{{ old('name', $team->name) }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="{{ __('cms.teams.name_placeholder') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" for="leader_id">
                    {{ __('cms.teams.leader') }} <span class="text-danger">*</span>
                </label>
                <select id="leader_id" name="leader_id"
                        class="form-select @error('leader_id') is-invalid @enderror">
                    @foreach($team->students as $member)
                        <option value="{{ $member->id }}" {{ $member->id === $team->leader_id ? 'selected' : '' }}>
                            {{ $member->name }} ({{ $member->university_id }})
                            @if($member->id === $team->leader_id)
                                ★
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('leader_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="cms-btn cms-btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Current Members ──────────────────────────────────────────────────────── --}}
<div class="cms-card mb-4" style="max-width:700px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-people-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.members') }} ({{ $team->students->count() }})</h3>
    </div>
    <div class="cms-card-body">
        @if($team->students->isEmpty())
            <p style="color:var(--cms-text-muted);">{{ __('cms.teams.no_members') }}</p>
        @else
            <ul class="list-unstyled mb-0">
                @foreach($team->students as $member)
                <li class="d-flex align-items-center justify-content-between py-2"
                    style="border-bottom:1px solid var(--cms-border);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem;">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="fw-semibold">{{ $member->name }}</span>
                            <span style="color:var(--cms-text-muted);font-size:.85rem;"> ({{ $member->university_id }})</span>
                            @if($member->id === $team->leader_id)
                                <span class="cms-badge cms-badge-success ms-1">{{ __('cms.teams.leader') }}</span>
                            @endif
                        </div>
                    </div>
                    @if($member->id !== $team->leader_id)
                    <form method="POST"
                          action="{{ route('doctor.teams.remove_member', [$level, $team, $member]) }}"
                          onsubmit="return confirm('{{ __('cms.teams.confirm_remove_member') }}')">
                        @csrf
                        <button type="submit" class="cms-btn cms-btn-danger"
                                style="padding:.25rem .6rem;font-size:.8rem;">
                            <i class="bi bi-person-dash"></i>
                        </button>
                    </form>
                    @endif
                </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- ── Add Members ──────────────────────────────────────────────────────────── --}}
@php
    $currentMemberIds = $team->students->pluck('id')->toArray();
    $available = $students->filter(fn($s) => !in_array($s->id, $currentMemberIds));
@endphp

@if($available->isNotEmpty())
<div class="cms-card" style="max-width:700px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-person-plus me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.add_members') }}</h3>
    </div>
    <div class="cms-card-body">
        <form method="POST" action="{{ route('doctor.teams.update', [$level, $team]) }}">
            @csrf
            {{-- Pass current name and leader so UpdateTeamRequest validates properly --}}
            <input type="hidden" name="name" value="{{ $team->name }}">
            <input type="hidden" name="leader_id" value="{{ $team->leader_id }}">

            <div style="max-height:200px;overflow-y:auto;border:1px solid var(--cms-border);border-radius:.5rem;padding:.75rem;background:var(--cms-bg-card);" class="mb-3">
                @foreach($available as $student)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                               name="add_student_ids[]" value="{{ $student->id }}"
                               id="add_{{ $student->id }}">
                        <label class="form-check-label" for="add_{{ $student->id }}">
                            {{ $student->name }}
                            <span style="color:var(--cms-text-muted);font-size:.85rem;">({{ $student->university_id }})</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="cms-btn cms-btn-primary">
                <i class="bi bi-person-plus"></i> {{ __('cms.teams.add_members') }}
            </button>
        </form>
    </div>
</div>
@endif

@endsection
