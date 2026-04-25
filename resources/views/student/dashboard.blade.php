@extends('layouts.app')

@section('title', __('cms.student.dashboard_title') . ' — CMS')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="cms-page-header">
    <div>
        <div class="cms-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <span>{{ __('cms.student.dashboard_title') }}</span>
        </div>
        <h1>{{ __('cms.student.welcome', ['name' => auth('student')->user()->name]) }}</h1>
        <p>{{ __('cms.student.portal_intro') }}</p>
    </div>
    <span class="cms-badge cms-badge-cyan align-self-start">
        <i class="bi bi-mortarboard-fill"></i> {{ __('Student') }}
    </span>
</div>

{{-- ── Profile Card ─────────────────────────────────────────────────────── --}}
<div class="row g-4">
    <div class="col-lg-5 col-md-7">
        <div class="cms-card">
            <div class="cms-card-header">
                <h3><i class="bi bi-person-fill me-2" style="color:#0AFFFF;"></i>{{ __('cms.student.dashboard_title') }}</h3>
            </div>
            <div class="cms-card-body">
                @php $s = auth('student')->user(); @endphp
                <div class="d-flex flex-column gap-3">
                    <div style="display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                        <i class="bi bi-card-text" style="color:#0AFFFF; font-size:1.1rem; width:20px; text-align:center;"></i>
                        <div>
                            <div style="font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.5px; font-weight:600;">
                                {{ __('cms.student.university_id') }}
                            </div>
                            <div style="font-family:'Courier New',monospace; color:#e2e8f0; margin-top:.1rem;">
                                {{ $s->university_id }}
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                        <i class="bi bi-envelope-fill" style="color:#0AFFFF; font-size:1.1rem; width:20px; text-align:center;"></i>
                        <div>
                            <div style="font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.5px; font-weight:600;">
                                {{ __('cms.student.email') }}
                            </div>
                            <div style="color:#e2e8f0; margin-top:.1rem;">{{ $s->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
