@extends('layouts.app')

@section('title', __('cms.teams.my_team_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('student.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            <span>{{ __('cms.teams.my_team_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.my_team_title') }}</h1>
    </div>
</div>

{{-- ── Not Assigned ─────────────────────────────────────────────────────────── --}}
@if(! $team)
    <div class="cms-card" style="text-align:center; padding: 5rem 2rem; border-radius: 20px; border: 1px dashed rgba(10, 255, 255, 0.3); background: radial-gradient(circle at top, rgba(10, 255, 255, 0.05), transparent 60%);">
        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; border-radius: 50%; background: rgba(10, 255, 255, 0.1); display: flex; align-items: center; justify-content: center; color: #0AFFFF;">
            <i class="bi bi-diagram-3" style="font-size:3rem;"></i>
        </div>
        <h3 style="color:var(--text-primary); margin-bottom:.5rem; font-weight: 700;">{{ __('cms.teams.not_assigned_title') }}</h3>
        <p style="color:var(--text-muted); max-width: 400px; margin: 0 auto;">{{ __('cms.teams.not_assigned_desc') }}</p>
    </div>
@else

{{-- ── Team Info ─────────────────────────────────────────────────────────────── --}}
<div class="row g-4">
    <div class="col-lg-7">
        {{-- Main Team Card --}}
        <div class="cms-card mb-4" style="border-radius: 16px; overflow: hidden;">
            <div class="cms-card-header" style="background: linear-gradient(135deg, rgba(10, 255, 255, 0.08), transparent); border-bottom: 1px solid var(--cms-border); padding: 1.5rem;">
                <h3 class="m-0 d-flex align-items-center gap-3">
                    <div class="icon-wrap" style="width: 45px; height: 45px; border-radius: 12px; background: rgba(10, 255, 255, 0.15); color: #0AFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                            {{ $team->name ?? __('cms.teams.unnamed') }}
                        </div>
                        @if($isLeader)
                            <div style="margin-top: 0.25rem;">
                                <span class="cms-badge cms-badge-cyan" style="font-size: 0.7rem;"><i class="bi bi-star-fill me-1"></i>{{ __('cms.teams.you_are_leader') }}</span>
                            </div>
                        @endif
                    </div>
                </h3>
            </div>
            
            <div class="cms-card-body p-4">
                {{-- Leader --}}
                <div class="mb-4 d-flex align-items-center gap-3 p-3" style="background: rgba(255,255,255,0.02); border: 1px solid var(--cms-border); border-radius: 12px;">
                    <div class="user-avatar" style="width: 46px; height: 46px; border-radius: 50%; background: rgba(10, 255, 255, 0.1); color: #0AFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 0 15px rgba(10, 255, 255, 0.15);">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:.75rem; color:var(--text-faint); text-transform:uppercase; letter-spacing:1px; font-weight: 600; margin-bottom: 0.2rem;">
                            {{ __('cms.teams.leader') }}
                        </div>
                        <div class="fw-bold" style="font-size: 1.05rem; color: var(--text-primary);">{{ $team->leader?->name ?? '—' }}</div>
                    </div>
                    <i class="bi bi-star-fill ms-auto" style="color: #0AFFFF; font-size: 1.5rem; opacity: 0.2;"></i>
                </div>

                {{-- Members --}}
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div style="font-size:.85rem; color:var(--text-faint); text-transform:uppercase; letter-spacing:1px; font-weight: 600;">
                            {{ __('cms.teams.members') }}
                        </div>
                        <span class="badge" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">{{ $team->students->count() }}</span>
                    </div>
                    
                    <div class="row g-3">
                        @foreach($team->students as $member)
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3 p-2" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; transition: all 0.2s;">
                                    <div class="user-avatar" style="width:36px; height:36px; border-radius: 50%; background: rgba(255,255,255,0.05); color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size:.9rem; font-weight: 500; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $member->name }}
                                        </div>
                                    </div>
                                    @if($member->id === $team->leader_id)
                                        <i class="bi bi-star-fill me-2" style="color:#0AFFFF; font-size:.85rem;" title="{{ __('cms.ui.leader') }}"></i>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Project --}}
        <div class="cms-card" style="border-radius: 16px;">
            <div class="cms-card-header" style="padding: 1.25rem 1.5rem;">
                <h3 class="m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-lightbulb-fill" style="color:#a78bfa;"></i> 
                    {{ __('cms.teams.assigned_project') }}
                </h3>
            </div>
            <div class="cms-card-body p-4">
                @if($team->currentProject)
                    <div class="p-4" style="background: linear-gradient(to right, rgba(167, 139, 250, 0.05), transparent); border-left: 4px solid #a78bfa; border-radius: 0 12px 12px 0;">
                        <div class="fw-bold mb-2" style="font-size: 1.15rem; color: var(--text-primary);">
                            {{ $team->currentProject->title }}
                        </div>
                        @if($team->currentProject->description)
                            <p style="color:var(--text-muted); font-size:.95rem; margin:0; line-height: 1.6;">
                                {{ $team->currentProject->description }}
                            </p>
                        @endif
                    </div>
                @else
                    <div class="d-flex align-items-center gap-3 p-3" style="background: rgba(255,255,255,0.02); border-radius: 12px; color: var(--text-muted);">
                        <i class="bi bi-info-circle text-info" style="font-size: 1.25rem;"></i>
                        <span>{{ __('cms.teams.no_project_yet') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Leader-only: Submit Change Request ─────────────────────────────── --}}
    @if($isLeader)
    <div class="col-lg-5">
        <div class="cms-card mb-4" style="border-radius: 16px;">
            <div class="cms-card-header" style="background: linear-gradient(135deg, rgba(122, 34, 253, 0.08), transparent); border-bottom: 1px solid var(--cms-border);">
                <h3 class="m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-send-fill" style="color:#a78bfa;"></i> 
                    {{ __('cms.teams.submit_request_title') }}
                </h3>
            </div>
            <div class="cms-card-body p-4">

                @php
                    $pendingReq = $team->requests()->where('status','pending')->first();
                @endphp

                @if($pendingReq)
                    <div class="cms-alert cms-alert-warning m-0 d-flex align-items-center gap-3" style="padding: 1.25rem;">
                        <i class="bi bi-clock-fill" style="font-size: 1.5rem;"></i>
                        <div style="font-weight: 500;">{{ __('cms.teams.pending_request_notice') }}</div>
                    </div>
                @else

                <form method="POST" action="{{ route('student.team.request') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="req_name" style="color: var(--text-primary);">
                            {{ __('cms.teams.requested_name') }}
                        </label>
                        <input type="text" id="req_name" name="requested_name"
                               value="{{ old('requested_name') }}"
                               class="cms-form-control @error('requested_name') is-invalid @enderror"
                               placeholder="{{ __('cms.teams.name_placeholder') }}">
                        @error('requested_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2" style="color:var(--text-faint); font-size: 0.85rem;">{{ __('cms.teams.request_name_hint') }}</div>
                    </div>

                    @if($availableProjects->isNotEmpty())
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="req_project" style="color: var(--text-primary);">
                            {{ __('cms.teams.requested_project') }}
                        </label>
                        <select id="req_project" name="project_idea_id"
                                class="cms-form-control @error('project_idea_id') is-invalid @enderror" style="appearance: auto; background-color: #0C101A; color: var(--text-primary);">
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

                    <div class="cms-alert cms-alert-info mb-4" style="padding: 0.75rem 1rem;">
                        <i class="bi bi-info-circle-fill"></i>
                        <div style="font-size: 0.85rem;">{{ __('cms.teams.request_at_least_one') }}</div>
                    </div>

                    <button type="submit" class="cms-btn cms-btn-primary w-100 justify-content-center py-2" style="font-size: 1rem;">
                        <i class="bi bi-send-fill me-2"></i> {{ __('cms.teams.submit_request_btn') }}
                    </button>
                </form>

                @endif
            </div>
        </div>

        {{-- Request History --}}
        @php $history = $team->requests()->with('reviewer')->latest()->take(5)->get(); @endphp
        @if($history->isNotEmpty())
        <div class="cms-card" style="border-radius: 16px;">
            <div class="cms-card-header">
                <h3 class="m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history" style="color:#a78bfa;"></i>
                    {{ __('cms.teams.request_history') }}
                </h3>
            </div>
            <div class="cms-card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($history as $req)
                    <div class="list-group-item bg-transparent" style="border-color: var(--cms-border); padding: 1.25rem;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                @if($req->status === 'pending')
                                    <span class="cms-badge" style="background:rgba(251,191,36,.15);color:#fbbf24; border: 1px solid rgba(251,191,36,0.3);"><i class="bi bi-hourglass-split"></i> {{ __('cms.teams.status_pending') }}</span>
                                @elseif($req->status === 'approved')
                                    <span class="cms-badge cms-badge-success"><i class="bi bi-check-circle"></i> {{ __('cms.teams.status_approved') }}</span>
                                @else
                                    <span class="cms-badge cms-badge-danger"><i class="bi bi-x-circle"></i> {{ __('cms.teams.status_rejected') }}</span>
                                @endif
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-faint);">
                                {{ $req->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        <div style="font-size:.9rem; color:var(--text-muted); line-height: 1.5; padding-left: 0.25rem;">
                            @if($req->requested_name) 
                                <div class="d-flex gap-2">
                                    <span class="text-faint fw-semibold">{{ __('cms.teams.name') }}:</span> 
                                    <span class="text-primary">{{ $req->requested_name }}</span>
                                </div>
                            @endif
                            @if($req->projectIdea) 
                                <div class="d-flex gap-2 mt-1">
                                    <span class="text-faint fw-semibold">{{ __('cms.teams.project') }}:</span> 
                                    <span class="text-primary">{{ $req->projectIdea->title }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endif

@endsection
