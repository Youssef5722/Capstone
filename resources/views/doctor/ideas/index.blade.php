@extends('layouts.app')

@section('title', __('cms.doctor.ideas_title') . ' — ' . $level->name . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('doctor.dashboard') }}" style="color:inherit;text-decoration:none;">
                <i class="bi bi-house-fill"></i>
            </a>
            <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
            <span>{{ __('cms.doctor.level_context', ['name' => $level->name]) }}</span>
            <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
            <span>{{ __('cms.doctor.ideas_title') }}</span>
        </div>
        <h1>{{ __('cms.doctor.ideas_title') }}</h1>
        <p>{{ __('cms.doctor.level_context', ['name' => $level->name]) }} &mdash; {{ $activeYear->name }}</p>
    </div>
    <a href="{{ route('doctor.ideas.create', $level->id) }}" class="cms-btn cms-btn-primary align-self-start">
        <i class="bi bi-plus-lg me-1"></i> {{ __('cms.doctor.add_idea') }}
    </a>
</div>

{{-- ── Ideas List ───────────────────────────────────────────────────────── --}}
<div class="cms-card">
    <div class="cms-card-body p-0">
        @if($ideas->isEmpty())
            <div class="cms-empty-state" style="padding:3rem;">
                <i class="bi bi-lightbulb"></i>
                <p>{{ __('cms.doctor.no_ideas') }}</p>
                <a href="{{ route('doctor.ideas.create', $level->id) }}" class="cms-btn cms-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('cms.doctor.add_idea') }}
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th style="width:3rem;">#</th>
                            <th>{{ __('cms.doctor.idea_title') }}</th>
                            <th>{{ __('cms.doctor.idea_description') }}</th>
                            <th style="width:9rem;">{{ __('cms.academic_years.name') }}</th>
                            <th style="width:10rem;">{{ __('cms.general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ideas as $i => $idea)
                        <tr>
                            <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                            <td style="font-weight:600;">{{ $idea->title }}</td>
                            <td style="color:var(--cms-text-muted);font-size:.9rem;">
                                {{ Str::limit($idea->description, 100) ?: '—' }}
                            </td>
                            <td style="font-size:.8rem;color:var(--cms-text-muted);">
                                {{ $idea->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('doctor.ideas.edit', [$level->id, $idea->id]) }}"
                                       class="cms-btn cms-btn-secondary cms-btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('doctor.ideas.destroy', [$level->id, $idea->id]) }}"
                                          onsubmit="return confirm('{{ __('cms.doctor.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cms-btn cms-btn-danger cms-btn-sm">
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
