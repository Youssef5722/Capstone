@extends('layouts.app')

@section('title', __('cms.teams.my_team_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('student.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.my_team_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.my_team_title') }}</h1>
    </div>
</div>

{{-- ── Not Assigned ─────────────────────────────────────────────────────────── --}}
@if(! $team)
    <div class="cms-card" style="text-align:center;padding:4rem 2rem;">
        <i class="bi bi-diagram-3" style="font-size:3.5rem;color:var(--cms-text-muted);display:block;margin-bottom:1.25rem;"></i>
        <h3 style="color:var(--cms-text);margin-bottom:.5rem;">{{ __('cms.teams.not_assigned_title') }}</h3>
        <p style="color:var(--cms-text-muted);">{{ __('cms.teams.not_assigned_desc') }}</p>
    </div>
@else

{{-- ── Team Info ─────────────────────────────────────────────────────────────── --}}
<div class="row g-4">
    <div class="col-lg-7">
        <div class="cms-card mb-4">
            <div class="cms-card-header">
                <h3>
                    <i class="bi bi-diagram-3-fill me-2" style="color:#a78bfa;"></i>
                    {{ $team->name ?? __('cms.teams.unnamed') }}
                    @if($isLeader)
                        <span class="cms-badge cms-badge-success ms-2">{{ __('cms.teams.you_are_leader') }}</span>
                    @endif
                </h3>
            </div>
            <div class="cms-card-body">
                {{-- Leader --}}
                <div class="mb-3 d-flex align-items-center gap-3">
                    <div class="user-avatar">{{ strtoupper(substr($team->leader?->name ?? '?', 0, 1)) }}</div>
                    <div>
                        <div style="font-size:.75rem;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:.05em;">
                            {{ __('cms.teams.leader') }}
                        </div>
                        <div class="fw-semibold">{{ $team->leader?->name ?? '—' }}</div>
                    </div>
                </div>

                {{-- Members --}}
                <div style="border-top:1px solid var(--cms-border);padding-top:1rem;">
                    <div style="font-size:.8rem;color:var(--cms-text-muted);margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.05em;">
                        {{ __('cms.teams.members') }} ({{ $team->students->count() }})
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($team->students as $member)
                            <div class="d-flex align-items-center gap-2 py-1 px-2"
                                 style="background:var(--cms-bg-card);border:1px solid var(--cms-border);border-radius:.5rem;">
                                <div class="user-avatar" style="width:26px;height:26px;font-size:.65rem;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <span style="font-size:.9rem;">{{ $member->name }}</span>
                                @if($member->id === $team->leader_id)
                                    <i class="bi bi-star-fill" style="color:#fbbf24;font-size:.7rem;"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Project --}}
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-lightbulb-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.assigned_project') }}</h3>
            </div>
            <div class="cms-card-body">
                @if($team->currentProject)
                    <div class="fw-semibold mb-1">{{ $team->currentProject->title }}</div>
                    @if($team->currentProject->description)
                        <p style="color:var(--cms-text-muted);font-size:.9rem;margin:0;">{{ $team->currentProject->description }}</p>
                    @endif
                @else
                    <p style="color:var(--cms-text-muted);">{{ __('cms.teams.no_project_yet') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Leader-only: Submit Change Request ─────────────────────────────── --}}
    @if($isLeader)
    <div class="col-lg-5">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-send me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.submit_request_title') }}</h3>
            </div>
            <div class="cms-card-body">

                @php
                    $pendingReq = $team->requests()->where('status','pending')->first();
                @endphp

                @if($pendingReq)
                    <div class="cms-alert cms-alert-warning">
                        <i class="bi bi-clock-fill"></i>
                        <div>{{ __('cms.teams.pending_request_notice') }}</div>
                    </div>
                @else

                <form method="POST" action="{{ route('student.team.request') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="req_name">
                            {{ __('cms.teams.requested_name') }}
                        </label>
                        <input type="text" id="req_name" name="requested_name"
                               value="{{ old('requested_name') }}"
                               class="form-control @error('requested_name') is-invalid @enderror"
                               placeholder="{{ __('cms.teams.name_placeholder') }}">
                        @error('requested_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" style="color:var(--cms-text-muted);">{{ __('cms.teams.request_name_hint') }}</div>
                    </div>

                    @if($availableProjects->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="req_project">
                            {{ __('cms.teams.requested_project') }}
                        </label>
                        <select id="req_project" name="project_idea_id"
                                class="form-select @error('project_idea_id') is-invalid @enderror">
                            <option value="">{{ __('cms.teams.no_change') }}</option>
                            @foreach($availableProjects as $idea)
                                <option value="{{ $idea->id }}"
                                        {{ old('project_idea_id') == $idea->id ? 'selected' : '' }}>
                                    {{ $idea->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_idea_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="form-text mb-3" style="color:var(--cms-text-muted);">
                        {{ __('cms.teams.request_at_least_one') }}
                    </div>

                    <button type="submit" class="cms-btn cms-btn-primary w-100 justify-content-center">
                        <i class="bi bi-send"></i> {{ __('cms.teams.submit_request_btn') }}
                    </button>
                </form>

                @endif
            </div>
        </div>

        {{-- Request History --}}
        @php $history = $team->requests()->with('reviewer')->latest()->take(5)->get(); @endphp
        @if($history->isNotEmpty())
        <div class="cms-card mt-4">
            <div class="cms-card-header">
                <h3><i class="bi bi-clock-history me-2" style="color:#a78bfa;"></i>{{ __('cms.teams.request_history') }}</h3>
            </div>
            <div class="cms-card-body">
                @foreach($history as $req)
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div>
                        @if($req->status === 'pending')
                            <span class="cms-badge" style="background:rgba(251,191,36,.15);color:#fbbf24;">{{ __('cms.teams.status_pending') }}</span>
                        @elseif($req->status === 'approved')
                            <span class="cms-badge cms-badge-success">{{ __('cms.teams.status_approved') }}</span>
                        @else
                            <span class="cms-badge cms-badge-danger">{{ __('cms.teams.status_rejected') }}</span>
                        @endif
                    </div>
                    <div style="font-size:.85rem;color:var(--cms-text-muted);">
                        @if($req->requested_name) {{ __('cms.teams.name') }}: {{ $req->requested_name }}<br>@endif
                        @if($req->projectIdea) {{ __('cms.teams.project') }}: {{ $req->projectIdea->title }}<br>@endif
                        {{ $req->created_at->format('Y-m-d') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endif

@endsection
