@extends('layouts.app')

@section('title', __('cms.teams.requests_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ $level->name }}</span>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.requests_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.requests_title') }}</h1>
        <p>{{ __('cms.teams.requests_intro', ['level' => $level->name]) }}</p>
    </div>
</div>

<div class="cms-card">
    <div class="cms-card-header">
        <h3>
            <i class="bi bi-inbox-fill me-2" style="color:#a78bfa;"></i>
            {{ __('cms.teams.requests_title') }}
            @php $pendingCount = $requests->where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="cms-badge cms-badge-warning ms-2">{{ $pendingCount }} pending</span>
            @endif
        </h3>
    </div>
    <div class="cms-card-body">
        @if($requests->isEmpty())
            <div class="cms-empty-state">
                <i class="bi bi-inbox"></i>
                <p>{{ __('cms.teams.no_requests') }}</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th>{{ __('cms.teams.request_team') }}</th>
                            <th>{{ __('cms.teams.requester') }}</th>
                            <th>{{ __('cms.teams.requested_name') }}</th>
                            <th>{{ __('cms.teams.requested_project') }}</th>
                            <th>{{ __('cms.teams.request_status') }}</th>
                            <th>{{ __('cms.general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $req->team->name ?? __('cms.teams.unnamed') }}</span>
                            </td>
                            <td>{{ $req->requester?->name ?? '—' }}</td>
                            <td>
                                @if($req->requested_name)
                                    <span style="color:var(--text-primary);">{{ $req->requested_name }}</span>
                                @else
                                    <span style="color:var(--text-faint);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($req->projectIdea)
                                    <span class="cms-badge cms-badge-purple">{{ Str::limit($req->projectIdea->title, 30) }}</span>
                                @else
                                    <span style="color:var(--text-faint);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="cms-badge" style="background:rgba(251,191,36,.15);color:#fbbf24;">
                                        <i class="bi bi-clock"></i> {{ __('cms.teams.status_pending') }}
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="cms-badge cms-badge-success">
                                        <i class="bi bi-check-circle"></i> {{ __('cms.teams.status_approved') }}
                                    </span>
                                @else
                                    <span class="cms-badge cms-badge-danger">
                                        <i class="bi bi-x-circle"></i> {{ __('cms.teams.status_rejected') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                <div class="d-flex gap-2">
                                    <form method="POST"
                                          action="{{ route('doctor.requests.approve', [$level, $req]) }}"
                                          onsubmit="return confirm('{{ __('cms.teams.confirm_approve_request') }}')">
                                        @csrf
                                        <button type="submit" class="cms-btn cms-btn-primary"
                                                style="padding:.3rem .7rem;font-size:.8rem;">
                                            <i class="bi bi-check-lg"></i> {{ __('cms.teams.approve_btn') }}
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('doctor.requests.reject', [$level, $req]) }}"
                                          onsubmit="return confirm('{{ __('cms.teams.confirm_reject_request') }}')">
                                        @csrf
                                        <button type="submit" class="cms-btn cms-btn-danger"
                                                style="padding:.3rem .7rem;font-size:.8rem;">
                                            <i class="bi bi-x-lg"></i> {{ __('cms.teams.reject_btn') }}
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <span style="color:var(--text-faint);font-size:.85rem;">
                                        {{ __('cms.teams.reviewed_at', ['date' => $req->reviewed_at?->format('Y-m-d')]) }}
                                    </span>
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

@endsection
