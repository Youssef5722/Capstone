<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CMS')</title>
    <!-- Bootstrap 5.3 CSS (LTR/RTL) -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="@yield('theme_class')">

@php
    $webUser     = auth('web')->user();
    $studentUser = auth('student')->user();
@endphp

<!-- Navigation -->
<nav class="auth-nav">
    <a href="{{ url('/') }}" class="auth-brand">CMS</a>

    <div class="auth-nav-links">
        <a href="{{ url('/') }}">Home</a>

        @if($webUser)
            <a href="{{ $webUser->role?->name === 'admin' ? route('admin.dashboard') : route('doctor.dashboard') }}">
                Dashboard
            </a>
        @elseif($studentUser)
            <a href="{{ route('student.dashboard') }}">Dashboard</a>
        @else
            {{-- Guest: Dashboard link goes nowhere useful, keep decorative --}}
            <a href="{{ url('/') }}">Dashboard</a>
        @endif
    </div>

    <div class="auth-nav-profile">

        <!-- Language Switcher (always visible, right-most) -->
        <div style="display:flex;align-items:center;gap:.35rem;">
            <form method="POST" action="{{ route('language.switch') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="locale" value="ar">
                <button type="submit" style="background:none;border:1px solid {{ app()->getLocale()==='ar' ? 'rgba(122,34,253,0.5)' : 'rgba(255,255,255,0.05)' }};color:{{ app()->getLocale()==='ar' ? '#a78bfa' : '#64748b' }};font-size:.75rem;font-weight:600;padding:.3rem .65rem;border-radius:6px;cursor:pointer;font-family:inherit;">ع</button>
            </form>
            <form method="POST" action="{{ route('language.switch') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="locale" value="en">
                <button type="submit" style="background:none;border:1px solid {{ app()->getLocale()==='en' ? 'rgba(122,34,253,0.5)' : 'rgba(255,255,255,0.05)' }};color:{{ app()->getLocale()==='en' ? '#a78bfa' : '#64748b' }};font-size:.75rem;font-weight:600;padding:.3rem .65rem;border-radius:6px;cursor:pointer;font-family:inherit;">EN</button>
            </form>
        </div>

        {{-- Logged-in: show username + logout icon --}}
        @if($webUser || $studentUser)
            @php
                $logoutRoute = $webUser ? route('logout') : route('student.logout');
                $userName    = $webUser ? $webUser->name : $studentUser->name;
            @endphp
            <span style="color:#a78bfa;font-size:.85rem;opacity:.85;white-space:nowrap;">
                {{ $userName }}
            </span>
            <form action="{{ $logoutRoute }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="icon-btn" title="Sign out"
                        style="background:none;border:none;cursor:pointer;padding:0;">
                    <i class="bi bi-box-arrow-right" style="color:#a78bfa;font-size:1.1rem;"></i>
                </button>
            </form>
        @endif
        {{-- Guest: nothing shown --}}

    </div>
</nav>

@yield('content')

<!-- Unified Footer -->
<footer class="bottom-footer">
    <div class="footer-brand">CMS</div>
    <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">University Guidelines</a>
    </div>
    <div class="footer-copy">&copy; {{ date('Y') }} Academic Management System. All rights reserved.</div>
</footer>

<!-- Bootstrap & Custom JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/auth.js') }}"></script>
@stack('scripts')
</body>
</html>
