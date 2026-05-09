@extends('layouts.app')

@section('title', __('cms.teams.index_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.dashboard') }}">{{ __('cms.nav.dashboard') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ $level->name }}</span>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.teams.index_title') }}</span>
        </div>
        <h1>{{ __('cms.teams.index_title') }}</h1>
        <p>{{ __('cms.teams.index_intro', ['level' => $level->name]) }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-self-start">
        <a href="{{ route('doctor.teams.distribute', $level) }}" class="cms-btn cms-btn-secondary">
            <i class="bi bi-shuffle"></i> {{ __('cms.teams.distribute_btn') }}
        </a>
        <a href="{{ route('doctor.teams.create', $level) }}" class="cms-btn cms-btn-primary">
            <i class="bi bi-plus-lg"></i> {{ __('cms.teams.create_btn') }}
        </a>
    </div>
</div>

{{-- ── Teams Table ──────────────────────────────────────────────────────────── --}}
<div class="cms-card">
    <div class="cms-card-header">
        <h3>
            <i class="bi bi-diagram-3-fill me-2" style="color:#a78bfa;"></i>
            {{ __('cms.teams.index_title') }}
            <span class="badge ms-2" style="background:rgba(167,139,250,.15);color:#a78bfa;font-size:.75rem;">{{ $teams->count() }}</span>
        </h3>
    </div>
    <div class="cms-card-body">
        @if($teams->isEmpty())
            <div style="text-align:center;padding:4rem 1rem;">
                <i class="bi bi-diagram-3" style="font-size:3rem;color:var(--cms-text-muted);display:block;margin-bottom:1rem;"></i>
                <p style="color:var(--cms-text-muted);font-size:1.1rem;">{{ __('cms.teams.no_teams') }}</p>
                <a href="{{ route('doctor.teams.create', $level) }}" class="cms-btn cms-btn-primary mt-2">
                    <i class="bi bi-plus-lg"></i> {{ __('cms.teams.create_btn') }}
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('cms.teams.name') }}</th>
                            <th>{{ __('cms.teams.leader') }}</th>
                            <th>{{ __('cms.teams.members_count') }}</th>
                            <th>{{ __('cms.teams.project') }}</th>
                            <th>{{ __('cms.general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teams as $team)
                        @php
                            $project = \DB::table('team_project')
                                ->where('team_id', $team->id)
                                ->join('project_ideas','project_ideas.id','=','team_project.project_idea_id')
                                ->select('project_ideas.title')
                                ->first();
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold" style="color:var(--cms-text);">
                                    {{ $team->name ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width:30px;height:30px;font-size:.75rem;">
                                        {{ strtoupper(substr($team->leader?->name ?? '?', 0, 1)) }}
                                    </div>
                                    {{ $team->leader?->name ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <span class="cms-badge cms-badge-info">{{ $team->students->count() }}</span>
                            </td>
                            <td>
                                @if($project)
                                    <span class="cms-badge cms-badge-success">{{ Str::limit($project->title, 30) }}</span>
                                @else
                                    <span style="color:var(--cms-text-muted);font-size:.85rem;">{{ __('cms.teams.no_project') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('doctor.teams.edit', [$level, $team]) }}"
                                       class="cms-btn cms-btn-secondary" style="padding:.35rem .75rem;font-size:.8rem;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('doctor.teams.destroy', [$level, $team]) }}"
                                          onsubmit="return confirm('{{ __('cms.teams.confirm_delete') }}')">
                                        @csrf
                                        <button type="submit" class="cms-btn cms-btn-danger" style="padding:.35rem .75rem;font-size:.8rem;">
                                            <i class="bi bi-trash"></i>
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
</div>

@endsection
