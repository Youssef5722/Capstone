@extends('layouts.auth')

@section('title', __('cms.auth.reset_password_title') . ' — CMS')
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
        <div class="auth-card" id="stuResetPasswordCard">

            <div class="auth-card-header">
                <h2 class="auth-card-title">{{ __('cms.auth.reset_password_title') }}</h2>
                <p class="auth-card-subtitle">{{ __('cms.auth.reset_password_desc') }}</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger mt-3" style="font-size:0.85rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.update') }}" class="mt-3">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-4 position-relative">
                    <label class="form-label" for="stuNewPassword">{{ __('cms.profile.new_password') }}</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock prefix-icon"></i>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" id="stuNewPassword"
                               placeholder="••••••••" minlength="8" required>
                        <button type="button" class="btn-toggle-password" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 position-relative">
                    <label class="form-label" for="stuConfirmPassword">{{ __('cms.profile.confirm_password') }}</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock prefix-icon"></i>
                        <input type="password" class="form-control"
                               name="password_confirmation" id="stuConfirmPassword"
                               placeholder="••••••••" minlength="8" required>
                        <button type="button" class="btn-toggle-password" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary-student w-100">
                    {{ __('cms.auth.reset_password_btn') }} <i class="bi bi-shield-lock ms-2"></i>
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
