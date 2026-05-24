@extends('layouts.app')

@section('title', __('cms.doctor.dashboard_title') . ' — CMS')

@push('styles')
<style>
/* ── Doctor Dashboard Specific Styles ── */

/* Hero / Welcome Banner */
.doc-hero {
    position: relative;
    background: linear-gradient(135deg,
        rgba(122, 34, 253, 0.18) 0%,
        rgba(10, 255, 255, 0.06) 50%,
        rgba(122, 34, 253, 0.08) 100%);
    border: 1px solid rgba(122, 34, 253, 0.25);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.doc-hero::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(122, 34, 253, 0.15) 0%, transparent 70%);
    pointer-events: none;
}

.doc-hero::after {
    content: '';
    position: absolute;
    bottom: -60px;
    left: 30%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(10, 255, 255, 0.08) 0%, transparent 70%);
    pointer-events: none;
}

.doc-hero-content {
    position: relative;
    z-index: 1;
}

.doc-hero-greeting {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #a78bfa;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.doc-hero h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.doc-hero h1 span {
    background: linear-gradient(135deg, #a78bfa, #0AFFFF);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.doc-hero-sub {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin: 0;
}

.doc-hero-meta {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.75rem;
}

/* KPI Stats Grid */
.doc-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.doc-stat-card {
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-radius: 16px;
    padding: 1.4rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s var(--ease-smooth);
    cursor: default;
    position: relative;
    overflow: hidden;
}

.doc-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    opacity: 0;
    transition: opacity 0.3s;
}

.doc-stat-card.purple::before { background: linear-gradient(90deg, #7A22FD, #a78bfa); }
.doc-stat-card.cyan::before   { background: linear-gradient(90deg, #0AFFFF, #38bdf8); }
.doc-stat-card.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
.doc-stat-card.amber::before  { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

.doc-stat-card:hover {
    transform: translateY(-4px);
    border-color: rgba(122, 34, 253, 0.3);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
}

.doc-stat-card:hover::before {
    opacity: 1;
}

.doc-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.doc-stat-icon.purple { background: rgba(122, 34, 253, 0.15); color: #a78bfa; }
.doc-stat-icon.cyan   { background: rgba(10, 255, 255, 0.12); color: #0AFFFF; }
.doc-stat-icon.green  { background: rgba(16, 185, 129, 0.12); color: #34d399; }
.doc-stat-icon.amber  { background: rgba(245, 158, 11, 0.12); color: #fbbf24; }

.doc-stat-body {}

.doc-stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    color: var(--text-primary);
    letter-spacing: -1px;
}

.doc-stat-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 0.3rem;
    font-weight: 500;
}

/* Section title row */
.doc-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.doc-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.doc-section-title i {
    color: #a78bfa;
    font-size: 1.1rem;
}

/* Level Cards (improved) */
.doc-level-card {
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-radius: 16px;
    padding: 0;
    overflow: hidden;
    transition: all 0.3s var(--ease-smooth);
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
}

.doc-level-card:hover {
    transform: translateY(-5px);
    border-color: rgba(122, 34, 253, 0.35);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(122, 34, 253, 0.15);
}

.doc-level-card-top {
    background: linear-gradient(135deg, rgba(122, 34, 253, 0.15), rgba(122, 34, 253, 0.05));
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-sub);
    position: relative;
    overflow: hidden;
}

.doc-level-card-top::after {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(122, 34, 253, 0.1);
    pointer-events: none;
}

.doc-level-name {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    position: relative;
    z-index: 1;
}

.doc-level-name i {
    color: #a78bfa;
}

.doc-level-card-body {
    padding: 1.25rem 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.doc-level-stat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 0.85rem;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--border-sub);
    font-size: 0.85rem;
}

.doc-level-stat-row .label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
}

.doc-level-stat-row .label i {
    font-size: 0.85rem;
    opacity: 0.7;
}

.doc-level-stat-row .value {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1rem;
}

.doc-level-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-sub);
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

/* Quick Actions */
.doc-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.85rem;
}

.doc-qa-card {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 1rem 1.2rem;
    border-radius: 12px;
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    text-decoration: none;
    transition: all 0.25s var(--ease-smooth);
    color: var(--text-muted);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.doc-qa-card::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.25s;
    border-radius: inherit;
}

.doc-qa-card.purple::before { background: linear-gradient(135deg, rgba(122,34,253,0.1), transparent); }
.doc-qa-card.cyan::before   { background: linear-gradient(135deg, rgba(10,255,255,0.08), transparent); }
.doc-qa-card.green::before  { background: linear-gradient(135deg, rgba(16,185,129,0.1), transparent); }
.doc-qa-card.amber::before  { background: linear-gradient(135deg, rgba(245,158,11,0.1), transparent); }

.doc-qa-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    color: var(--text-primary);
}

.doc-qa-card.purple:hover { border-color: rgba(122,34,253,0.4); }
.doc-qa-card.cyan:hover   { border-color: rgba(10,255,255,0.3); }
.doc-qa-card.green:hover  { border-color: rgba(16,185,129,0.3); }
.doc-qa-card.amber:hover  { border-color: rgba(245,158,11,0.3); }

.doc-qa-card:hover::before { opacity: 1; }

.doc-qa-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.doc-qa-icon.purple { background: rgba(122,34,253,0.15); color: #a78bfa; }
.doc-qa-icon.cyan   { background: rgba(10,255,255,0.12); color: #0AFFFF; }
.doc-qa-icon.green  { background: rgba(16,185,129,0.12); color: #34d399; }
.doc-qa-icon.amber  { background: rgba(245,158,11,0.12); color: #fbbf24; }

.doc-qa-text {
    position: relative;
    z-index: 1;
    flex: 1;
}

.doc-qa-text .qa-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.3;
}

.doc-qa-text .qa-sub {
    font-size: 0.72rem;
    color: var(--text-faint);
    margin-top: 0.15rem;
}

.doc-qa-arrow {
    position: relative;
    z-index: 1;
    color: var(--text-faint);
    font-size: 0.8rem;
    transition: transform 0.25s;
}

.doc-qa-card:hover .doc-qa-arrow {
    transform: translateX(4px);
    color: var(--text-muted);
}

/* No levels empty state */
.doc-empty-hero {
    text-align: center;
    padding: 4rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.doc-empty-hero .empty-icon-wrap {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(122, 34, 253, 0.08);
    border: 1px solid rgba(122, 34, 253, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: #a78bfa;
    animation: float-bob 3s ease-in-out infinite;
}

@keyframes float-bob {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

.doc-empty-hero h4 {
    font-size: 1.1rem;
    color: var(--text-primary);
}

.doc-empty-hero p {
    font-size: 0.88rem;
    color: var(--text-faint);
    max-width: 320px;
    margin: 0;
}

/* Animated counter */
.counter-value {
    display: inline-block;
}

/* RTL adjustments */
[dir="rtl"] .doc-hero-meta {
    align-items: flex-start;
}

[dir="rtl"] .doc-qa-arrow {
    transform: scaleX(-1);
}

[dir="rtl"] .doc-qa-card:hover .doc-qa-arrow {
    transform: scaleX(-1) translateX(4px);
}

/* Responsive */
@media (max-width: 768px) {
    .doc-hero {
        padding: 1.5rem;
        flex-direction: column;
        align-items: flex-start;
    }
    .doc-hero h1 { font-size: 1.5rem; }
    .doc-hero-meta { align-items: flex-start; }
    .doc-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .doc-quick-actions { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .doc-stats-grid { grid-template-columns: 1fr 1fr; }
    .doc-stat-value { font-size: 1.6rem; }
}
</style>
@endpush

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
            <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
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
                                    <i class="bi bi-arrow-right ms-auto"></i>
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
        <i class="bi bi-chevron-right doc-qa-arrow"></i>
    </a>

    <a href="{{ route('doctor.teams.index', $firstLevel->id) }}" class="doc-qa-card cyan">
        <div class="doc-qa-icon cyan">
            <i class="bi bi-diagram-3-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.teams.index_title') }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_teams_sub') ?? 'View & manage teams' }}</div>
        </div>
        <i class="bi bi-chevron-right doc-qa-arrow"></i>
    </a>

    <a href="{{ route('doctor.requests.index', $firstLevel->id) }}" class="doc-qa-card amber">
        <div class="doc-qa-icon amber">
            <i class="bi bi-inbox-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.teams.requests_title') }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_requests_sub') ?? 'Review pending requests' }}</div>
        </div>
        <i class="bi bi-chevron-right doc-qa-arrow"></i>
    </a>

    <a href="{{ route('doctor.workspaces.index', $firstLevel->id) }}" class="doc-qa-card green">
        <div class="doc-qa-icon green">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div class="doc-qa-text">
            <div class="qa-label">{{ __('cms.workspace.nav') }}</div>
            <div class="qa-sub">{{ __('cms.doctor.qa_workspace_sub') ?? 'Workspaces & phases' }}</div>
        </div>
        <i class="bi bi-chevron-right doc-qa-arrow"></i>
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
