@extends('layouts.auth')

@section('title', __('cms.auth.student_portal_tag') . ' — ' . __('cms.auth.tab_login') . ' — CMS')
@section('theme_class', 'student-theme')

@section('content')

@php
    $jsWelcomeBack   = e(__('cms.auth.welcome_back'));
    $jsSignInStudent = e(__('cms.auth.sign_in_student'));
    $jsFillDetails   = e(__('cms.auth.fill_details'));
    $jsStuRegTitle   = e(__('cms.auth.stu_reg_title'));
@endphp

<div class="auth-wrapper">

    <!-- Left Branding Area -->
    <div class="auth-sidebar">
        <div class="brand-tag student">{{ strtoupper(__('cms.auth.student_portal_tag')) }}</div>
        <h1 class="auth-heading">{{ __('cms.auth.heading_line1') }} <br><span class="text-gradient-student">{{ __('cms.auth.heading_gradient') }}</span><br>{{ __('cms.auth.heading_line3') }}</h1>
        <p class="auth-subheading">{{ __('cms.auth.student_subheading') }}</p>
    </div>

    <!-- Right Form Area -->
    <div class="auth-form-container">
        <div class="auth-card large" id="studentAuthCard">

            <div class="auth-card-header">
                <h2 class="auth-card-title" id="cardTitle">{{ __('cms.auth.welcome_back') }}</h2>
                <p class="auth-card-subtitle" id="cardSubtitle">{{ __('cms.auth.sign_in_student') }}</p>
                @if(session('success'))
                    <div class="alert alert-success mt-3" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="auth-tabs">
                <a href="{{ route('student.login') }}" class="auth-tab active student-theme" data-panel="panelLogin">{{ __('cms.auth.tab_login') }}</a>
                <a href="{{ route('student.activate') }}" class="auth-tab" data-panel="panelActivate">{{ __('cms.auth.tab_signup') }}</a>
            </div>

            <div class="form-panels-wrapper" id="formPanelsWrapper">

                <!-- PANEL: LOGIN (Visible) -->
                <div class="form-panel panel-visible" id="panelLogin"
                     data-title-html="{{ $jsWelcomeBack }}"
                     data-title-class="auth-card-title"
                     data-subtitle="{{ $jsSignInStudent }}"
                     data-page-title="{{ __('cms.auth.student_portal_tag') }} — {{ __('cms.auth.tab_login') }} — CMS">

                    <form class="needs-validation" method="POST" action="{{ route('student.login') }}" novalidate id="studentLoginForm">
                        @csrf
                        <div class="mb-4 position-relative">
                            <label class="form-label" for="university_id">{{ __('cms.auth.uni_id_email') }}</label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-person-badge prefix-icon"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="university_id"
                                       placeholder="e.g. STU-2024-XXXX@university.edu" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">{{ __('cms.auth.uni_id_email') }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 position-relative">
                            <label class="form-label" for="loginPassword">{{ __('cms.auth.password_label') }}</label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-lock prefix-icon"></i>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="loginPassword"
                                       placeholder="{{ __('cms.auth.password_placeholder') }}" required>
                                <button type="button" class="btn-toggle-password" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="#" class="btn-forgot">{{ __('cms.auth.forgot_password') }}</a>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">{{ __('cms.auth.password_label') }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary-student">
                            {{ __('cms.auth.sign_in_btn') }} <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="auth-footer-text">
                        {{ __('cms.auth.not_activated') }}
                        <a href="{{ route('student.activate') }}" class="switch-panel-link" data-panel="panelActivate">{{ __('cms.auth.activate_here') }}</a>
                    </div>
                </div>

                <!-- PANEL: ACTIVATE (Hidden) -->
                <div class="form-panel panel-hidden" id="panelActivate"
                     data-title-html='<i class="bi bi-circle-fill" style="font-size:8px;vertical-align:middle;"></i> {{ strtoupper($jsStuRegTitle) }}'
                     data-title-class="auth-card-title register-title student"
                     data-subtitle="{{ $jsFillDetails }}"
                     data-page-title="{{ __('cms.auth.activate_btn') }} — CMS">

                    <form class="needs-validation" method="POST" action="{{ route('student.activate') }}" novalidate id="activateForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="actEmail">{{ __('cms.auth.email_label') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-envelope prefix-icon"></i>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="actEmail"
                                           placeholder="name@university.edu" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.email_label') }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="actPassword">{{ __('cms.auth.password_label') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-lock prefix-icon"></i>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="actPassword"
                                           placeholder="••••••••" required>
                                    <button type="button" class="btn-toggle-password" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.password_label') }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="actConfirmPassword">{{ __('cms.auth.confirm_password') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-lock prefix-icon"></i>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation"
                                           id="actConfirmPassword" placeholder="••••••••" required>
                                    <button type="button" class="btn-toggle-password" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.confirm_password') }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="actCode">{{ __('cms.auth.activation_code') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-key prefix-icon"></i>
                                    <input type="text" class="form-control @error('activation_code') is-invalid @enderror" name="activation_code" id="actCode"
                                           placeholder="{{ __('cms.auth.activation_code_placeholder') }}" value="{{ old('activation_code') }}" required>
                                </div>
                                @error('activation_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.activation_code') }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-primary-student mt-2">{{ __('cms.auth.activate_btn') }}</button>
                    </form>

                    <div class="auth-footer-text" style="font-size: 0.8rem; margin-top: 2rem;">
                        {{ __('cms.auth.already_account') }}
                        <a href="{{ route('student.login') }}" class="switch-panel-link" data-panel="panelLogin">{{ __('cms.auth.tab_login') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
