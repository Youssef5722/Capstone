@extends('layouts.app')

@section('title', __('cms.doctor.dashboard_title') . ' — CMS')

@section('content')

@php
    /* ── Aggregate stats across all assigned levels ── */
    $totalStudents = 0;
    $totalIdeas    = 0;
    $totalLevels   = $assignments->count();

    if ($activeYear) {
        foreach ($assignments as $a) {
            if (isset($a->level->students)) {
                $totalStudents += $a->level->students->where('academic_year_id', $activeYear->id)->count();
            }
            if (isset($a->level->ideas)) {
                $totalIdeas += $a->level->ideas->where('academic_year_id', $activeYear->id)->count();
            }
        }
    }

    /* ── Time-based greeting ── */
    $hour = now()->hour;
    if ($hour < 12)       $timeGreeting = __('cms.doctor.greeting_morning')  ?? 'Good Morning';
    elseif ($hour < 17)   $timeGreeting = __('cms.doctor.greeting_afternoon') ?? 'Good Afternoon';
    else                  $timeGreeting = __('cms.doctor.greeting_evening')   ?? 'Good Evening';
@endphp

{{-- ════════════════════════════════════════════════
     HERO / WELCOME BANNER
════════════════════════════════════════════════ --}}
<div class="doc-hero">
    <div class="doc-hero-content">
        <div class="doc-hero-greeting">
            <i class="bi bi-stars"></i>
            {{ $timeGreeting }}
        </div>
        <h1>
            {{ __('cms.doctor.welcome') ?? 'Hello,' }}
            <span>{{ $doctor->name }}</span> 👋
        </h1>
        <p class="doc-hero-sub">
            {{ __('cms.doctor.dashboard_intro') }}
        </p>
    </div>

    <div class="doc-hero-meta">
        @if($activeYear)
            <span class="cms-badge cms-badge-success" style="font-size:0.8rem; padding:0.45rem 1rem;">
                <i class="bi bi-calendar-check-fill"></i>
                {{ __('cms.doctor.active_year_badge', ['name' => $activeYear->name]) }}
            </span>
        @else
            <span class="cms-badge cms-badge-danger" style="font-size:0.8rem; padding:0.45rem 1rem;">
                <i class="bi bi-calendar-x-fill"></i>
                {{ __('cms.doctor.no_active_year_badge') }}
            </span>
        @endif

        <div class="cms-breadcrumb" style="justify-content: flex-end;">
            <i class="bi bi-house-fill"></i>
            <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size:0.6rem;"></i>
            <span>{{ __('cms.doctor.dashboard_title') }}</span>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     KPI STATS ROW
════════════════════════════════════════════════ --}}
@if($activeYear)
<div class="doc-stats-grid">
    {{-- Assigned Levels --}}
    <div class="doc-stat-card purple">
        <div class="doc-stat-icon purple">
            <i class="bi bi-layers-fill"></i>
        </div>
        <div class="doc-stat-body">
            <div class="doc-stat-value counter-value" data-target="{{ $totalLevels }}">{{ $totalLevels }}</div>
            <div class="doc-stat-label">{{ __('cms.doctor.my_levels') ?? 'Assigned Levels' }}</div>
        </div>
    </div>

    {{-- Total Students --}}
    <div class="doc-stat-card cyan">
        <div class="doc-stat-icon cyan">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="doc-stat-body">
            <div class="doc-stat-value counter-value" data-target="{{ $totalStudents }}">{{ $totalStudents }}</div>
            <div class="doc-stat-label">{{ __('cms.doctor.stat_students') ?? 'Total Students' }}</div>
        </div>
    </div>

    {{-- Project Ideas --}}
    <div class="doc-stat-card green">
        <div class="doc-stat-icon green">
            <i class="bi bi-lightbulb-fill"></i>
        </div>
        <div class="doc-stat-body">
            <div class="doc-stat-value counter-value" data-target="{{ $totalIdeas }}">{{ $totalIdeas }}</div>
            <div class="doc-stat-label">{{ __('cms.doctor.stat_ideas') ?? 'Project Ideas' }}</div>
        </div>
    </div>

    {{-- Academic Year --}}
    <div class="doc-stat-card amber">
        <div class="doc-stat-icon amber">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="doc-stat-body">
            <div class="doc-stat-value" style="font-size:1.1rem; letter-spacing:0;">{{ $activeYear->name }}</div>
            <div class="doc-stat-label">{{ __('cms.doctor.stat_year') ?? 'Active Year' }}</div>
        </div>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════
     ASSIGNED LEVELS
════════════════════════════════════════════════ --}}
<div class="doc-section-header">
    <div class="doc-section-title">
        <i class="bi bi-layers-fill"></i>
        {{ __('cms.doctor.my_levels') }}
    </div>
    @if(!$assignments->isEmpty())
        <span class="cms-badge cms-badge-purple">
            {{ $totalLevels }} {{ $totalLevels == 1 ? 'Level' : 'Levels' }}
        </span>
    @endif
</div>

<div class="cms-card" style="margin-bottom: 2rem;">
    <div class="cms-card-body">
        @if(!$activeYear)
            <div class="cms-alert cms-alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ __('cms.doctor.no_active_year') }}</div>
            </div>

        @elseif($assignments->isEmpty())
            <div class="doc-empty-hero">
                <div class="empty-icon-wrap">
                    <i class="bi bi-clipboard-x"></i>
                </div>
                <h4>{{ __('cms.doctor.no_levels_title') ?? 'No Levels Assigned Yet' }}</h4>
                <p>{{ __('cms.doctor.no_levels_assigned') }}</p>
            </div>

        @else
            <div class="row g-4">
                @foreach($assignments as $assignment)
                    @php
                        $studCount = isset($assignment->level->students)
                            ? $assignment->level->students->where('academic_year_id', $activeYear?->id)->count()
                            : 0;
                        $ideaCount = isset($assignment->level->ideas)
                            ? $assignment->level->ideas->where('academic_year_id', $activeYear?->id)->count()
                            : 0;
                    @endphp
                    <div class="col-md-6 col-xl-4">
                        <div class="doc-level-card">
                            {{-- Card Top --}}
                            <div class="doc-level-card-top">
                                <div class="doc-level-name">
                                    <i class="bi bi-bookmark-fill"></i>
                                    {{ $assignment->level->name }}
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="doc-level-card-body">
                                {{-- Students Stat --}}
                                <div class="doc-level-stat-row">
                                    <div class="label">
                                        <i class="bi bi-people" style="color:#0AFFFF;"></i>
                                        <span>{{ __('cms.doctor.students_in_year', ['year' => $activeYear?->name]) ?? 'Students ('.$activeYear?->name.')' }}</span>
                                    </div>
                                    <span class="value">{{ $studCount }}</span>
                                </div>

                                {{-- Ideas Stat --}}
                                @if(isset($assignment->level->ideas))
                                <div class="doc-level-stat-row">
                                    <div class="label">
                                        <i class="bi bi-lightbulb" style="color:#34d399;"></i>
                                        <span>{{ __('cms.doctor.ideas_count') ?? 'Project Ideas' }}</span>
                                    </div>
                                    <span class="value">{{ $ideaCount }}</span>
                                </div>
                                @endif
                            </div>

                            {{-- Card Footer (Action Buttons) --}}
                            <div class="doc-level-card-footer">
                                <a href="{{ route('doctor.students.index', $assignment->level->id) }}"
                                   class="cms-btn cms-btn-primary w-100 justify-content-center">
                                    <i class="bi bi-people-fill"></i>
                                    {{ __('cms.doctor.manage_level') ?? 'Manage Students' }}
                                    <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-auto"></i>
                                </a>
                                <a href="{{ route('doctor.workspaces.index', $assignment->level->id) }}"
                                   class="cms-btn cms-btn-ghost w-100 justify-content-center"
                                   style="border-color: rgba(122,34,253,0.2); color: #a78bfa;">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                    {{ __('cms.workspace.nav') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ════════════════════════════════════════════════
     QUICK ACTIONS (only when levels are assigned)
════════════════════════════════════════════════ --}}
@if($activeYear && !$assignments->isEmpty())
<div class="doc-section-header">
    <div class="doc-section-title">
        <i class="bi bi-lightning-charge-fill"></i>
        {{ __('cms.doctor.quick_actions') ?? 'Quick Actions' }}
    </div>
</div>

<div class="doc-quick-actions" style="margin-bottom: 2rem;">
    {{-- First level quick actions --}}
    @php $firstLevel = $assignments->first()->level; @endphp

    <a href="{{ route('doctor.ideas.index', $firstLevel->id) }}" class="doc-qa-card purple">
        <div class="doc-qa-icon purple">
            <i class="bi bi-lightbulb-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.doctor.qa_ideas') ?? 'Project Ideas' }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_ideas_sub') ?? 'Browse & manage ideas' }}</div>
        </div>
        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} doc-qa-arrow"></i>
    </a>

    <a href="{{ route('doctor.teams.index', $firstLevel->id) }}" class="doc-qa-card cyan">
        <div class="doc-qa-icon cyan">
            <i class="bi bi-diagram-3-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.teams.index_title') }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_teams_sub') ?? 'View & manage teams' }}</div>
        </div>
        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} doc-qa-arrow"></i>
    </a>

    <a href="{{ route('doctor.requests.index', $firstLevel->id) }}" class="doc-qa-card amber">
        <div class="doc-qa-icon amber">
            <i class="bi bi-inbox-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.teams.requests_title') }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_requests_sub') ?? 'Review pending requests' }}</div>
        </div>
        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} doc-qa-arrow"></i>
    </a>

    <a href="{{ route('doctor.workspaces.index', $firstLevel->id) }}" class="doc-qa-card green">
        <div class="doc-qa-icon green">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.workspace.nav') }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_workspace_sub') ?? 'Workspaces & phases' }}</div>
        </div>
        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} doc-qa-arrow"></i>
    </a>
</div>
@endif

@endsection

@push('scripts')
<script>
/* ── Animated counters ── */
document.addEventListener('DOMContentLoaded', function () {
    const counters = document.querySelectorAll('.counter-value[data-target]');

    const animateCounter = (el) => {
        const target = parseInt(el.dataset.target, 10);
        if (isNaN(target) || target === 0) return;

        let start = 0;
        const duration = 900;
        const startTime = performance.now();

        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target;
        };

        requestAnimationFrame(step);
    };

    // Intersection Observer for entrance animation
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        counters.forEach(c => observer.observe(c));
    } else {
        counters.forEach(animateCounter);
    }
});
</script>
@endpush
