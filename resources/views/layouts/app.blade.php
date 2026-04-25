<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CMS — Capstone Management System')</title>
    <!-- Bootstrap 5 (LTR/RTL) -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Dashboard Design System (extends auth.css tokens) -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

@php
    $webUser     = auth('web')->user();
    $studentUser = auth('student')->user();
    $isAdmin     = $webUser && $webUser->role?->name === 'admin';
    $isDoctor    = $webUser && $webUser->role?->name === 'doctor';
    $isStudent   = (bool) $studentUser;
    $currentUser = $webUser ?? $studentUser;
    $userInitials = $currentUser ? strtoupper(substr($currentUser->name, 0, 1)) : '?';
    $roleName    = $isAdmin ? 'Admin' : ($isDoctor ? 'Doctor' : ($isStudent ? 'Student' : ''));
    $logoutRoute = $isStudent ? route('student.logout') : route('logout');
@endphp

<!-- ── Mobile Overlay ─────────────────────────────────────── -->
<div class="cms-overlay" id="cmsOverlay"></div>

<!-- ══════════════════════════════════════════════════════════
     TOP NAVIGATION BAR
══════════════════════════════════════════════════════════ -->
<nav class="cms-nav">
    <!-- Left: hamburger + brand -->
    <div class="d-flex align-items-center gap-2">
        <button class="cms-nav-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <a href="/" class="cms-brand">
            <span class="cms-brand-dot"></span>CMS
        </a>
    </div>

    <!-- Right: lang switcher + user pill + logout -->
    <div class="cms-nav-right">
        <!-- Language Switcher -->
        <div class="cms-lang-switch">
            <form method="POST" action="{{ route('language.switch') }}">
                @csrf
                <input type="hidden" name="locale" value="ar">
                <button type="submit" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">ع</button>
            </form>
            <form method="POST" action="{{ route('language.switch') }}">
                @csrf
                <input type="hidden" name="locale" value="en">
                <button type="submit" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</button>
            </form>
        </div>

        @if($currentUser)
        <!-- User Pill -->
        <div class="cms-user-pill d-none d-md-flex">
            <div class="user-avatar">{{ $userInitials }}</div>
            <div>
                <div class="user-name">{{ $currentUser->name }}</div>
                <div class="user-role">{{ $roleName }}</div>
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ $logoutRoute }}">
            @csrf
            <button type="submit" class="cms-logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-md-inline">{{ __('cms.nav.logout') }}</span>
            </button>
        </form>
        @endif
    </div>
</nav>

<!-- ══════════════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════════════ -->
@if($currentUser)
<aside class="cms-sidebar" id="cmsSidebar">
    <nav class="cms-sidebar-nav">

        @if($isAdmin)
        <!-- Admin Navigation -->
        <span class="cms-nav-section">{{ __('cms.nav.dashboard') }}</span>
        <a href="{{ route('admin.dashboard') }}" class="cms-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> {{ __('cms.nav.dashboard') }}
        </a>

        <span class="cms-nav-section">{{ __('cms.doctors.title') }}</span>
        <a href="{{ route('admin.doctors.pending') }}" class="cms-nav-item {{ request()->routeIs('admin.doctors.pending') ? 'active' : '' }}">
            <i class="bi bi-person-check"></i> {{ __('cms.doctors.pending_title') }}
        </a>
        <a href="{{ route('admin.doctors.rejected') }}" class="cms-nav-item {{ request()->routeIs('admin.doctors.rejected') ? 'active' : '' }}">
            <i class="bi bi-person-x"></i> {{ __('cms.doctors.rejected_title') }}
            @php $rejectedCount = \App\Models\User::whereHas('role', fn($q) => $q->where('name','doctor'))->where('status','rejected')->count(); @endphp
            @if($rejectedCount > 0)
                <span class="badge bg-danger ms-1">{{ $rejectedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.doctors.index') }}" class="cms-nav-item {{ request()->routeIs('admin.doctors.index', 'admin.doctors.assign*', 'admin.doctors.assignments*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> {{ __('cms.doctors.approved_title') }}
        </a>

        <span class="cms-nav-section">{{ __('cms.academic_years.title') }}</span>
        <a href="{{ route('admin.academic-years.index') }}" class="cms-nav-item {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> {{ __('cms.academic_years.title') }}
        </a>

        @elseif($isDoctor)
        <!-- Doctor Navigation -->
        <span class="cms-nav-section">{{ __('cms.nav.dashboard') }}</span>
        <a href="{{ route('doctor.dashboard') }}" class="cms-nav-item {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> {{ __('cms.nav.dashboard') }}
        </a>

        @elseif($isStudent)
        <!-- Student Navigation -->
        <span class="cms-nav-section">{{ __('cms.nav.dashboard') }}</span>
        <a href="{{ route('student.dashboard') }}" class="cms-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> {{ __('cms.nav.dashboard') }}
        </a>
        @endif

    </nav>

    <!-- Sidebar Logout -->
    <div class="cms-sidebar-logout">
        <form method="POST" action="{{ $logoutRoute }}">
            @csrf
            <button type="submit">
                <i class="bi bi-box-arrow-left"></i> {{ __('cms.nav.logout') }}
            </button>
        </form>
    </div>
</aside>
@endif

<!-- ══════════════════════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════════════════ -->
<main class="cms-main" id="cmsMain">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="cms-alert cms-alert-success" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <div class="cms-alert-content">{{ session('success') }}</div>
        <button class="cms-alert-dismiss" onclick="this.closest('.cms-alert').remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="cms-alert cms-alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="cms-alert-content">{{ session('error') }}</div>
        <button class="cms-alert-dismiss" onclick="this.closest('.cms-alert').remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
    @endif
    @if(session('info'))
    <div class="cms-alert cms-alert-info" role="alert">
        <i class="bi bi-info-circle-fill"></i>
        <div class="cms-alert-content">{{ session('info') }}</div>
        <button class="cms-alert-dismiss" onclick="this.closest('.cms-alert').remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
    @endif
    @if($errors->any())
    <div class="cms-alert cms-alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="cms-alert-content">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button class="cms-alert-dismiss" onclick="this.closest('.cms-alert').remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
    @endif

    @yield('content')
</main>

<!-- ══════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════ -->
<footer class="cms-footer">
    <div class="cms-footer-brand">CMS</div>
    <nav class="cms-footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">University Guidelines</a>
    </nav>
    <div>&copy; {{ date('Y') }} Capstone Management System</div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('cmsSidebar');
    const overlay = document.getElementById('cmsOverlay');
    if (!sidebar || !toggle) return;

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('show'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

    toggle.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);
})();
</script>
@stack('scripts')
</body>
</html>
