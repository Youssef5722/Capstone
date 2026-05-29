@extends('layouts.auth')

@section('title', __('cms.auth.forgot_password_title') . ' — CMS')
@section('theme_class', 'student-theme')

@section('content')
<div class="auth-wrapper">

    <!-- Left Branding Area -->
    <div class="auth-sidebar">
        <div class="brand-tag student">{{ strtoupper(__('cms.auth.student_portal_tag')) }}</div>
        <h1 class="auth-heading">{{ __('cms.auth.heading_line1') }} <br><span class="text-gradient-student">{{ __('cms.auth.heading_gradient') }}</span><br>{{ __('cms.auth.heading_line3') }}</h1>
        <p class="auth-subheading">{{ __('cms.auth.student_subheading') }}</p>
    </div>

    <!-- Right Form Area -->
    <div class="auth-form-container">
        <div class="auth-card" id="stuForgotPasswordCard">

            <div class="auth-card-header">
                <h2 class="auth-card-title">{{ __('cms.auth.forgot_password_title') }}</h2>
                <p class="auth-card-subtitle">{{ __('cms.auth.forgot_password_desc') }}</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success mt-3" style="font-size:0.85rem;">
                    <i class="bi bi-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mt-3" style="font-size:0.85rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.email') }}" class="mt-3">
                @csrf
                <div class="mb-4 position-relative">
                    <label class="form-label" for="stuFpEmail">{{ __('cms.auth.email_for_reset') }}</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-envelope prefix-icon"></i>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" id="stuFpEmail"
                               placeholder="{{ __('cms.auth.email_placeholder') }}"
                               value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-primary-student w-100">
                    {{ __('cms.auth.send_reset_link') }} <i class="bi bi-send ms-2"></i>
                </button>
            </form>

            <div class="auth-footer-text mt-4">
                <a href="{{ route('student.login') }}">
                    <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} me-1"></i>
                    {{ __('cms.auth.back_to_login') }}
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
