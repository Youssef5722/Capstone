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
    <style>
    /* ── Navbar user dropdown ───────────────────── */
    .nav-user-dropdown {
        background: var(--surface-card);
        border: 1px solid var(--border-sub);
        border-radius: 14px;
        min-width: 200px;
        padding: 0.4rem;
        box-shadow: 0 16px 40px rgba(0,0,0,0.45);
        margin-top: 0.5rem !important;
    }
    .nav-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.85rem;
        border-radius: 9px;
        font-size: 0.83rem;
        font-weight: 500;
        font-family: var(--font);
        color: var(--text-muted);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        border: none;
        background: none;
        width: 100%;
        cursor: pointer;
        text-align: start;
    }
    .nav-dropdown-item i { font-size: 0.9rem; flex-shrink: 0; }
    .nav-dropdown-item:hover {
        background: rgba(122,34,253,0.1);
        color: #c4b5fd;
    }
    .nav-dropdown-item:hover i { color: #a78bfa; }
    .nav-dropdown-item.danger { color: #f87171; }
    .nav-dropdown-item.danger:hover {
        background: rgba(239,68,68,0.1);
        color: #f87171;
    }
    .nav-dropdown-item.danger i { color: #f87171; }
    .nav-dd-divider {
        height: 1px;
        background: var(--border-sub);
        margin: 0.3rem 0.2rem;
    }
    .nav-dd-header {
        padding: 0.35rem 0.85rem 0.2rem;
        font-size: 0.66rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-faint);
    }
    </style>
    @stack('styles')
</head>
<body>

@php
    use Illuminate\Support\Facades\Storage as LayoutStorage;
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
        @php
            $avatarPath = $currentUser->avatar ?? null;
            $profileRoute = $isStudent ? route('student.profile.show') : route('profile.show');
        @endphp
        <!-- User Pill Dropdown -->
        <div class="dropdown d-none d-md-block">
            <button class="cms-user-pill border-0 dropdown-toggle" type="button"
                    id="navUserDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false" style="cursor:pointer;">
                <div class="user-avatar overflow-hidden">
                    @if($avatarPath)
                        <img src="{{ LayoutStorage::url($avatarPath) }}"
                             alt="{{ $currentUser->name }}"
                             style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        {{ $userInitials }}
                    @endif
                </div>
                <div>
                    <div class="user-name">{{ $currentUser->name }}</div>
                    <div class="user-role">{{ $roleName }}</div>
                </div>
            </button>
            <ul class="dropdown-menu nav-user-dropdown dropdown-menu-end" aria-labelledby="navUserDropdown">
                <li class="nav-dd-header">{{ $currentUser->name }}</li>
                <li>
                    <a class="nav-dropdown-item" href="{{ $profileRoute }}">
                        <i class="bi bi-person-gear"></i>
                        {{ __('cms.profile.view_profile') }}
                    </a>
                </li>
                <li><div class="nav-dd-divider"></div></li>
                <li>
                    <form method="POST" action="{{ $logoutRoute }}">
                        @csrf
                        <button type="submit" class="nav-dropdown-item danger">
                            <i class="bi bi-box-arrow-right"></i>
                            {{ __('cms.nav.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
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

        @isset($level)
        <span class="cms-nav-section" style="margin-top: 1rem;">{{ __('cms.doctor.level_context', ['name' => $level->name]) }}</span>
        <a href="{{ route('doctor.students.index', $level) }}" class="cms-nav-item {{ request()->routeIs('doctor.students.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Student Management
        </a>
        <a href="{{ route('doctor.ideas.index', $level) }}" class="cms-nav-item {{ request()->routeIs('doctor.ideas.*') ? 'active' : '' }}">
            <i class="bi bi-lightbulb"></i> Project Ideas
        </a>
        <a href="{{ route('doctor.teams.index', $level) }}" class="cms-nav-item {{ request()->routeIs('doctor.teams.*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> {{ __('cms.teams.index_title') }}
        </a>
        <a href="{{ route('doctor.requests.index', $level) }}" class="cms-nav-item {{ request()->routeIs('doctor.requests.*') ? 'active' : '' }}">
            <i class="bi bi-inbox"></i> {{ __('cms.teams.requests_title') }}
        </a>
        <a href="{{ route('doctor.workspaces.index', $level) }}" class="cms-nav-item {{ request()->routeIs('doctor.workspaces.*', 'doctor.phases.*', 'doctor.tasks.*', 'doctor.submissions.*', 'doctor.task.comments.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> {{ __('cms.workspace.nav') }}
        </a>

        <span class="cms-nav-section" style="margin-top: 1rem;">{{ __('cms.general.actions') ?? 'Actions' }}</span>
        <a href="{{ route('doctor.dashboard') }}" class="cms-nav-item">
            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> {{ __('cms.doctor.back_to_dashboard') }}
        </a>
        @endisset

        @elseif($isStudent)
        <!-- Student Navigation -->
        <span class="cms-nav-section">{{ __('cms.nav.dashboard') }}</span>
        <a href="{{ route('student.dashboard') }}" class="cms-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> {{ __('cms.nav.dashboard') }}
        </a>
        <a href="{{ route('student.team.show') }}" class="cms-nav-item {{ request()->routeIs('student.team.*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> {{ __('cms.teams.my_team_title') }}
        </a>
        <a href="{{ route('student.workspace.show') }}" class="cms-nav-item {{ request()->routeIs('student.workspace.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> {{ __('cms.workspace.nav') }}
        </a>
        <a href="{{ route('student.profile.show') }}" class="cms-nav-item {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> {{ __('cms.profile.title') }}
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
        <a href="#">{{ __('cms.ui.privacy_policy') }}</a>
        <a href="#">{{ __('cms.ui.terms_of_service') }}</a>
        <a href="#">{{ __('cms.ui.university_guidelines') }}</a>
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
