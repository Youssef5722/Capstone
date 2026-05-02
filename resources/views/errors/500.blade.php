@extends('layouts.app')

@section('title', '500 — Server Error | CMS')

@section('content')
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;gap:1.5rem;">
    <div style="font-size:6rem;font-weight:800;color:var(--cms-danger,#f87171);line-height:1;">500</div>
    <h1 style="font-size:1.75rem;margin:0;">{{ __('cms.errors.server_error_title') ?? 'Internal Server Error' }}</h1>
    <p style="color:var(--cms-text-muted);max-width:480px;margin:0;">
        {{ __('cms.errors.server_error_message') ?? 'Something went wrong on our end. Please try again later.' }}
    </p>
    <a href="{{ url('/') }}" class="cms-btn cms-btn-primary">
        <i class="bi bi-house-fill me-2"></i>{{ __('cms.general.back_home') ?? 'Back to Home' }}
    </a>
</div>
@endsection
