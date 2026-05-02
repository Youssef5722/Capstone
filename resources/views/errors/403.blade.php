@extends('layouts.app')

@section('title', '403 — Forbidden | CMS')

@section('content')
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;gap:1.5rem;">
    <div style="font-size:6rem;font-weight:800;color:var(--cms-danger,#f87171);line-height:1;">403</div>
    <h1 style="font-size:1.75rem;margin:0;">{{ __('cms.errors.forbidden_title') ?? 'Access Forbidden' }}</h1>
    <p style="color:var(--cms-text-muted);max-width:480px;margin:0;">
        {{ __('cms.errors.forbidden_message') ?? 'You do not have permission to access this page.' }}
    </p>
    <a href="{{ url('/') }}" class="cms-btn cms-btn-primary">
        <i class="bi bi-house-fill me-2"></i>{{ __('cms.general.back_home') ?? 'Back to Home' }}
    </a>
</div>
@endsection
