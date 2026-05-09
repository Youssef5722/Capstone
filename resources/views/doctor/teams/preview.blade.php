@extends('layouts.app')

@section('title', __('cms.teams.preview_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('doctor.teams.index', $level) }}">{{ __('cms.teams.index_title') }}</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('doctor.teams.distribute', $level) }}">{{ __('cms.teams.distribute_title') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.preview_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.preview_title') }}</h1>
        <p>
            {{ __('cms.teams.preview_intro') }}
            — <span class="fw-semibold">{{ $mode === 'balanced' ? __('cms.teams.mode_balanced') : __('cms.teams.mode_fixed') }}</span>,
            {{ __('cms.teams.team_size') }}: <span class="fw-semibold">{{ $teamSize }}</span>
        </p>
    </div>
</div>

{{-- ── Summary Bar ──────────────────────────────────────────────────────────── --}}
<div class="d-flex gap-3 flex-wrap mb-4">
    <div class="cms-card" style="flex:1;min-width:160px;padding:1rem 1.5rem;">
        <div style="font-size:1.75rem;font-weight:700;color:#a78bfa;">{{ count($groups) }}</div>
        <div style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.teams.groups_to_create') }}</div>
    </div>
    <div class="cms-card" style="flex:1;min-width:160px;padding:1rem 1.5rem;">
        <div style="font-size:1.75rem;font-weight:700;color:#34d399;">
            {{ array_sum(array_map('count', $groups)) }}
        </div>
        <div style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.teams.students_to_assign') }}</div>
    </div>
    @if(count($remaining) > 0)
    <div class="cms-card" style="flex:1;min-width:160px;padding:1rem 1.5rem;border-color:rgba(251,191,36,.3);">
        <div style="font-size:1.75rem;font-weight:700;color:#fbbf24;">{{ count($remaining) }}</div>
        <div style="color:var(--cms-text-muted);font-size:.9rem;">{{ __('cms.teams.remaining_students') }}</div>
    </div>
    @endif
</div>

{{-- ── Generated Groups ─────────────────────────────────────────────────────── --}}
<div class="cms-card mb-4">
    <div class="cms-card-header">
        <h3><i class="bi bi-diagram-3 me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.generated_groups') }}</h3>
    </div>
    <div class="cms-card-body">
        <div class="row g-3">
            @foreach($groups as $i => $group)
            <div class="col-md-6 col-lg-4">
                <div style="background:var(--cms-bg-card);border:1px solid var(--cms-border);border-radius:.75rem;padding:1.25rem;">
                    <div class="fw-semibold mb-2" style="color:#a78bfa;">
                        <i class="bi bi-collection"></i> {{ __('cms.teams.group_n', ['n' => $i + 1]) }}
                        <span class="badge ms-1" style="background:rgba(167,139,250,.15);color:#a78bfa;font-size:.7rem;">
                            {{ count($group) }} {{ __('cms.teams.members') }}
                        </span>
                    </div>
                    <ul class="list-unstyled mb-0">
                        @foreach($group as $j => $student)
                        <li class="d-flex align-items-center gap-2 py-1">
                            <div class="user-avatar" style="width:26px;height:26px;font-size:.65rem;">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <span style="font-size:.9rem;">{{ $student->name }}</span>
                            @if($j === 0)
                                <span class="cms-badge cms-badge-success" style="font-size:.65rem;padding:.15rem .4rem;">
                                    {{ __('cms.teams.auto_leader') }}
                                </span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Remaining Students (fixed mode only) ───────────────────────────────── --}}
@if(count($remaining) > 0)
<div class="cms-card mb-4" style="border-color:rgba(251,191,36,.3);">
    <div class="cms-card-header">
        <h3><i class="bi bi-person-exclamation me-2" style="color:#fbbf24;"></i>{{ __('cms.teams.remaining_students') }}</h3>
    </div>
    <div class="cms-card-body">
        <div class="cms-alert cms-alert-warning mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ __('cms.teams.remaining_note') }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($remaining as $student)
                <span class="cms-badge" style="background:rgba(251,191,36,.15);color:#fbbf24;padding:.4rem .8rem;border-radius:.5rem;">
                    <i class="bi bi-person me-1"></i>{{ $student->name }}
                </span>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Confirm / Back ───────────────────────────────────────────────────────── --}}
<div class="d-flex gap-3 flex-wrap">
    <form method="POST" action="{{ route('doctor.teams.distribute.confirm', $level) }}">
        @csrf
        <button type="submit" class="cms-btn cms-btn-primary"
                onclick="return confirm('{{ __('cms.teams.confirm_distribute') }}')">
            <i class="bi bi-check-circle"></i> {{ __('cms.teams.confirm_btn') }}
        </button>
    </form>
    <a href="{{ route('doctor.teams.distribute', $level) }}" class="cms-btn cms-btn-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('cms.general.back') }}
    </a>
</div>

@endsection
