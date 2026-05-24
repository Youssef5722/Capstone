@extends('layouts.app')

@section('title', __('cms.phases.edit_title') . ' — CMS')

@section('content')

<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}">{{ $workspace->team->name ?? __('cms.teams.unnamed') }}</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ __('cms.phases.edit_title') }}</span>
        </div>
        <h1>{{ __('cms.phases.edit_title') }}: {{ $phase->title }}</h1>
    </div>
</div>

<div class="cms-card" style="max-width:680px;">
    <div class="cms-card-header">
        <h3><i class="bi bi-layers-fill me-2" style="color:#a78bfa;"></i>{{ __('cms.phases.edit_title') }}</h3>
    </div>
    <div class="cms-card-body">

        <form method="POST" action="{{ route('doctor.phases.update', [$level, $workspace, $phase]) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" for="phase_title">{{ __('cms.phases.name') }}</label>
                <input type="text" id="phase_title" name="title"
                       value="{{ old('title', $phase->title) }}"
                       class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="phase_desc">{{ __('cms.phases.description') }}</label>
                <textarea id="phase_desc" name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $phase->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phase_start">{{ __('cms.phases.start_date') }}</label>
                    <input type="date" id="phase_start" name="start_date"
                           value="{{ old('start_date', $phase->start_date->format('Y-m-d')) }}"
                           class="form-control @error('start_date') is-invalid @enderror" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phase_end">{{ __('cms.phases.end_date') }}</label>
                    <input type="date" id="phase_end" name="end_date"
                           value="{{ old('end_date', $phase->end_date->format('Y-m-d')) }}"
                           class="form-control @error('end_date') is-invalid @enderror" required>
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phase_status">{{ __('cms.phases.status') }}</label>
                    <select id="phase_status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['upcoming','active','completed'] as $st)
                            <option value="{{ $st }}" {{ old('status', $phase->status) === $st ? 'selected' : '' }}>
                                {{ __('cms.phases.status_' . $st) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phase_order">{{ __('cms.phases.order') }}</label>
                    <input type="number" id="phase_order" name="order"
                           value="{{ old('order', $phase->order) }}" min="1" max="255"
                           class="form-control @error('order') is-invalid @enderror" required>
                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="cms-btn cms-btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('cms.general.save') }}
                </button>
                <a href="{{ route('doctor.workspaces.show', [$level, $workspace]) }}" class="cms-btn cms-btn-secondary">
                    <i class="bi bi-x-lg"></i> {{ __('cms.general.cancel') }}
                </a>
            </div>
        </form>

    </div>
</div>

@endsection
