@extends('layouts.app')

@section('title', __('cms.doctor.edit_idea') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <a href="{{ route('doctor.dashboard') }}" style="color:inherit;text-decoration:none;">
                <i class="bi bi-house-fill"></i>
            </a>
            <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
            <a href="{{ route('doctor.ideas.index', $level->id) }}" style="color:inherit;text-decoration:none;">
                {{ __('cms.doctor.ideas_title') }}
            </a>
            <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
            <span>{{ __('cms.doctor.edit_idea') }}</span>
        </div>
        <h1>{{ __('cms.doctor.edit_idea') }}</h1>
        <p>{{ __('cms.doctor.level_context', ['name' => $level->name]) }} &mdash; {{ $activeYear->name }}</p>
    </div>
</div>

{{-- ── Edit Form ────────────────────────────────────────────────────────── --}}
<div class="cms-card" style="max-width:720px;">
    <div class="cms-card-header">
        <h3>
            <i class="bi bi-pencil-square me-2" style="color:#a78bfa;"></i>
            {{ __('cms.doctor.edit_idea') }}
        </h3>
    </div>
    <div class="cms-card-body">
        <form method="POST"
              action="{{ route('doctor.ideas.update', [$level->id, $idea->id]) }}"
              novalidate>
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="mb-4">
                <label class="form-label" for="ideaTitle">
                    {{ __('cms.doctor.idea_title') }} <span style="color:var(--cms-danger);">*</span>
                </label>
                <input type="text"
                       class="form-control @error('title') is-invalid @enderror"
                       name="title"
                       id="ideaTitle"
                       value="{{ old('title', $idea->title) }}"
                       maxlength="255"
                       required>
                @error('title')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="form-label" for="ideaDescription">
                    {{ __('cms.doctor.idea_description') }}
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          name="description"
                          id="ideaDescription"
                          rows="5">{{ old('description', $idea->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="cms-btn cms-btn-primary">
                    <i class="bi bi-check-lg me-1"></i> {{ __('cms.general.save') }}
                </button>
                <a href="{{ route('doctor.ideas.index', $level->id) }}" class="cms-btn cms-btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('cms.general.back') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
