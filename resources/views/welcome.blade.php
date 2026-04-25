@extends('layouts.auth')

@section('title', 'CMS — Capstone Management System')
@section('theme_class', '')

@section('content')
<div class="choose-container">

    {{-- ── LOGGED-IN STATE ──────────────────────────────────────────── --}}
    @php
        $webUser     = auth('web')->user();
        $studentUser = auth('student')->user();
        $isLoggedIn  = $webUser || $studentUser;
    @endphp

    @if($isLoggedIn)

        @php
            if ($webUser) {
                $displayName   = $webUser->name;
                $roleLabel     = $webUser->role?->name === 'admin' ? 'Admin' : 'Doctor';
                $dashRoute     = $webUser->role?->name === 'admin'
                                    ? route('admin.dashboard')
                                    : route('doctor.dashboard');
                $logoutRoute   = route('logout');
                $iconClass     = $webUser->role?->name === 'admin'
                                    ? 'bi bi-shield-fill-check'
                                    : 'bi bi-book-fill';
                $gradientClass = 'text-gradient-doctor';
            } else {
                $displayName   = $studentUser->name;
                $roleLabel     = 'Student';
                $dashRoute     = route('student.dashboard');
                $logoutRoute   = route('student.logout');
                $iconClass     = 'bi bi-mortarboard-fill';
                $gradientClass = 'text-gradient-student';
            }
        @endphp

        <div class="choose-headings">
            <div class="brand-tag" style="background: rgba(139,92,246,0.1); color: #a78bfa; border-color: rgba(139,92,246,0.25);">
                {{ strtoupper(__('cms.auth.already_signed_in')) }}
            </div>
            <h1 class="auth-heading">
                {{ __('cms.auth.welcome_back_user', ['name' => '']) }}<span class="{{ $gradientClass }}">{{ $displayName }}</span>
            </h1>
            <p class="auth-subheading m-auto">
                {{ __('cms.auth.signed_in_as', ['role' => $roleLabel]) }}
            </p>
        </div>

        <div class="choose-cards" style="max-width: 520px; margin: 0 auto;">
            {{-- Go to Dashboard card --}}
            <a href="{{ $dashRoute }}" class="role-card doctor" style="text-decoration: none;">
                <div class="role-icon">
                    <i class="{{ $iconClass }}"></i>
                </div>
                <h3>{{ __('cms.auth.go_to_dashboard') }}</h3>
                <p>{{ __('cms.auth.continue_workspace', ['role' => $roleLabel]) }}</p>
                <div class="role-arrow">
                    <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </div>
            </a>

            {{-- Sign out card --}}
            <form action="{{ $logoutRoute }}" method="POST" style="display:contents;">
                @csrf
                <button type="submit" class="role-card student"
                        style="all:unset; display:flex; align-items:center; gap:1.25rem;
                               cursor:pointer; width:100%; text-align:start;">
                    <div class="role-icon" style="flex-shrink:0;">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <div style="flex:1;">
                        <h3 style="margin:0 0 .25rem; font-size:1.2rem;">{{ __('cms.auth.sign_out') }}</h3>
                        <p style="margin:0; font-size:.9rem; opacity:.75;">
                            {{ __('cms.auth.end_session') }}
                        </p>
                    </div>
                    <div class="role-arrow">
                        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                    </div>
                </button>
            </form>
        </div>

    {{-- ── GUEST STATE (not logged in) ──────────────────────────────── --}}
    @else

        <div class="choose-headings">
            <div class="brand-tag" style="background: rgba(10,255,255,0.1); color: var(--student-primary); border-color: rgba(10,255,255,0.2);">
                {{ strtoupper(__('cms.auth.welcome_to_cms')) }}
            </div>
            <h1 class="auth-heading">{{ __('cms.auth.who_are_you') }} <span class="text-gradient-student">{{ __('cms.auth.today') }}</span></h1>
            <p class="auth-subheading m-auto">
                {{ __('cms.auth.choose_role') }}
            </p>
        </div>

        <div class="choose-cards">
            {{-- Student Card --}}
            <a href="{{ route('student.login') }}" class="role-card student">
                <div class="role-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h3>{{ __('cms.auth.role_student') }}</h3>
                <p>{{ __('cms.auth.role_student_desc') }}</p>
                <div class="role-arrow">
                    <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </div>
            </a>

            {{-- Doctor Card --}}
            <a href="{{ route('login') }}" class="role-card doctor">
                <div class="role-icon">
                    <i class="bi bi-book-fill"></i>
                </div>
                <h3>{{ __('cms.auth.role_doctor') }}</h3>
                <p>{{ __('cms.auth.role_doctor_desc') }}</p>
                <div class="role-arrow">
                    <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </div>
            </a>
        </div>

    @endif

</div>
@endsection
