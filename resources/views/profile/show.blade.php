@extends('layouts.app')

@section('title', __('cms.profile.title') . ' — CMS')

@push('styles')
<style>
/* ── Profile Page — Scoped Styles ───────────────────────── */
.profile-shell {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.5rem;
    min-height: calc(100vh - var(--nav-h) - var(--footer-h) - 4rem);
}

/* LEFT PANEL */
.profile-left {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.profile-hero {
    position: relative;
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-radius: 20px;
    overflow: hidden;
    text-align: center;
    padding-bottom: 1.75rem;
}

.profile-hero-banner {
    height: 80px;
    background: linear-gradient(135deg, #3b0764 0%, #7a22fd 55%, #9333ea 100%);
    position: relative;
}

.profile-hero-banner::after {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.profile-avatar-wrap {
    position: relative;
    display: inline-block;
    margin-top: -48px;
    margin-bottom: 0.75rem;
}

.profile-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 3px solid var(--surface-card);
    background: linear-gradient(135deg, var(--accent-admin), #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    font-weight: 700;
    color: #fff;
    overflow: hidden;
    position: relative;
    box-shadow: 0 8px 24px rgba(122, 34, 253, 0.35);
    transition: box-shadow 0.3s;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar-btn {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--accent-admin);
    border: 2px solid var(--surface-card);
    color: #fff;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(122,34,253,0.5);
}

.profile-avatar-btn:hover {
    background: var(--accent-admin-hover);
    transform: scale(1.1);
}

.profile-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    padding: 0 1.25rem;
}

.profile-email {
    font-size: 0.78rem;
    color: var(--text-faint);
    margin-bottom: 0.75rem;
}

/* Stat row in left panel */
.profile-meta {
    display: flex;
    justify-content: center;
    gap: 0;
    border-top: 1px solid var(--border-sub);
    margin-top: 0.5rem;
}

.profile-meta-item {
    flex: 1;
    text-align: center;
    padding: 0.85rem 0.5rem;
    position: relative;
}

.profile-meta-item + .profile-meta-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    height: 60%;
    width: 1px;
    background: var(--border-sub);
}

.profile-meta-item .meta-val {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    display: block;
    line-height: 1;
}

.profile-meta-item .meta-lbl {
    font-size: 0.65rem;
    color: var(--text-faint);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.2rem;
    display: block;
}

/* Quick info card */
.profile-info-card {
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-radius: 16px;
    overflow: hidden;
}

.profile-info-card-header {
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid var(--border-sub);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-faint);
}

.profile-info-card-header i {
    color: var(--accent-admin);
    font-size: 0.85rem;
}

.profile-info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    transition: background 0.15s;
}

.profile-info-row:last-child {
    border-bottom: none;
}

.profile-info-row:hover {
    background: rgba(255,255,255,0.02);
}

.profile-info-row .info-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(122,34,253,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    color: #a78bfa;
    flex-shrink: 0;
}

.profile-info-row .info-content .info-label {
    font-size: 0.68rem;
    color: var(--text-faint);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: block;
}

.profile-info-row .info-content .info-value {
    font-size: 0.82rem;
    color: var(--text-primary);
    font-weight: 500;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}

/* RIGHT PANEL */
.profile-right {
    display: flex;
    flex-direction: column;
    gap: 0;
}

/* Custom pill tabs */
.profile-tabs {
    display: flex;
    gap: 0.35rem;
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-radius: 14px 14px 0 0;
    padding: 0.6rem;
    border-bottom: none;
}

.profile-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.65rem 1rem;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: var(--text-faint);
    font-size: 0.82rem;
    font-weight: 600;
    font-family: var(--font);
    cursor: pointer;
    transition: all 0.2s var(--ease-smooth);
    white-space: nowrap;
}

.profile-tab-btn i {
    font-size: 0.9rem;
}

.profile-tab-btn:hover:not(.active) {
    background: rgba(255,255,255,0.04);
    color: var(--text-muted);
}

.profile-tab-btn.active {
    background: linear-gradient(135deg, rgba(122,34,253,0.25), rgba(122,34,253,0.1));
    color: #c4b5fd;
    border: 1px solid rgba(122,34,253,0.3);
    box-shadow: 0 2px 12px rgba(122,34,253,0.15);
}

/* Panel card */
.profile-panel {
    background: var(--surface-card);
    border: 1px solid var(--border-sub);
    border-top: none;
    border-radius: 0 0 16px 16px;
    padding: 2rem;
    flex: 1;
}

/* Section label inside form */
.profile-section-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-faint);
    margin-bottom: 1rem;
    padding-bottom: 0.6rem;
    border-bottom: 1px solid var(--border-sub);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.profile-section-label i { color: var(--accent-admin); }

/* Input group with icon */
.profile-input-group {
    position: relative;
    margin-bottom: 1.1rem;
}

.profile-input-group label {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-faint);
    margin-bottom: 0.45rem;
}

.profile-input-wrap {
    position: relative;
}

.profile-input-wrap .field-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-faint);
    font-size: 0.85rem;
    pointer-events: none;
    z-index: 1;
    left: 1rem;
}

[dir="rtl"] .profile-input-wrap .field-icon {
    left: auto;
    right: 1rem;
}

.profile-input-wrap input {
    width: 100%;
    background: rgba(12, 16, 26, 0.8);
    border: 1px solid rgba(255,255,255,0.07);
    color: var(--text-primary);
    padding: 0.72rem 1rem 0.72rem 2.6rem;
    border-radius: 10px;
    font-size: 0.88rem;
    font-family: var(--font);
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    outline: none;
}

[dir="rtl"] .profile-input-wrap input {
    padding-left: 1rem;
    padding-right: 2.6rem;
}

.profile-input-wrap input:focus {
    border-color: rgba(122,34,253,0.6);
    box-shadow: 0 0 0 3px rgba(122,34,253,0.12);
    background: rgba(12, 16, 26, 1);
}

.profile-input-wrap input:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.profile-input-wrap input.is-invalid {
    border-color: var(--accent-danger);
    box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
}

.invalid-feedback {
    font-size: 0.75rem;
    color: #f87171;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.invalid-feedback::before {
    content: '\F330';
    font-family: 'bootstrap-icons';
    font-size: 0.8rem;
}

/* Read-only locked field */
.profile-locked-field {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 1rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 10px;
    font-size: 0.88rem;
    color: var(--text-faint);
}

.profile-locked-field .lock-icon {
    color: rgba(255,255,255,0.15);
    font-size: 0.8rem;
    flex-shrink: 0;
}

/* Levels pills area */
.profile-levels-area {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.75rem;
    background: rgba(122,34,253,0.04);
    border: 1px solid rgba(122,34,253,0.12);
    border-radius: 10px;
    min-height: 48px;
    align-items: center;
}

/* Save button */
.profile-save-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.72rem 1.75rem;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--accent-admin), #9333ea);
    color: #fff;
    font-size: 0.88rem;
    font-weight: 600;
    font-family: var(--font);
    border: none;
    cursor: pointer;
    transition: all 0.25s var(--ease-smooth);
    box-shadow: 0 4px 16px rgba(122,34,253,0.3);
    letter-spacing: 0.2px;
}

.profile-save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(122,34,253,0.45);
}

.profile-save-btn:active { transform: translateY(0); }

.profile-save-btn.green-btn {
    background: linear-gradient(135deg, #059669, #10b981);
    box-shadow: 0 4px 16px rgba(16,185,129,0.3);
}

.profile-save-btn.green-btn:hover {
    box-shadow: 0 8px 24px rgba(16,185,129,0.45);
}

/* Password strength indicator */
.pw-strength {
    display: flex;
    gap: 4px;
    margin-top: 0.5rem;
}

.pw-strength-bar {
    flex: 1;
    height: 3px;
    border-radius: 2px;
    background: rgba(255,255,255,0.08);
    transition: background 0.3s;
}

.pw-strength-bar.weak   { background: #ef4444; }
.pw-strength-bar.medium { background: #f59e0b; }
.pw-strength-bar.strong { background: #10b981; }

.pw-strength-label {
    font-size: 0.7rem;
    color: var(--text-faint);
    margin-top: 0.3rem;
}

/* Toggle password eye */
.profile-eye-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 0.9rem;
    background: none;
    border: none;
    color: var(--text-faint);
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0;
    line-height: 1;
    transition: color 0.2s;
    z-index: 2;
}

[dir="rtl"] .profile-eye-btn {
    right: auto;
    left: 0.9rem;
}

.profile-eye-btn:hover { color: var(--text-muted); }

/* Responsive */
@media (max-width: 900px) {
    .profile-shell {
        grid-template-columns: 1fr;
    }
    .profile-left {
        flex-direction: row;
        flex-wrap: wrap;
    }
    .profile-hero { flex: 1; min-width: 260px; }
    .profile-info-card { flex: 1; min-width: 260px; }
}
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    $isDoctor   = $user->role?->name === 'doctor';
    $isAdmin    = $user->role?->name === 'admin';
    $roleName   = $isAdmin ? __('cms.auth.role_admin') : __('cms.auth.role_doctor');
    $accentGrad = 'linear-gradient(135deg,#7a22fd,#a855f7)';
    $levels     = is_array($user->requested_levels) ? $user->requested_levels : [];
    $levelCount = count($levels);
    $roleIcon   = $isAdmin ? 'shield-fill' : 'book-fill';
@endphp

<div class="container-fluid py-4 px-3 px-lg-4">
<div class="profile-shell">

    {{-- ══════════════════════════════════════════
         LEFT PANEL
    ══════════════════════════════════════════ --}}
    <div class="profile-left">

        {{-- Hero Card --}}
        <div class="profile-hero">
            <div class="profile-hero-banner" style="background:{{ $accentGrad }};"></div>

            <div class="profile-avatar-wrap">
                <div class="profile-avatar">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>

                <button class="profile-avatar-btn" id="triggerAvatarUpload"
                        title="{{ __('cms.profile.avatar_upload') }}">
                    <i class="bi bi-camera-fill"></i>
                </button>

                <form id="avatarForm" method="POST" action="{{ route('profile.avatar') }}"
                      enctype="multipart/form-data" class="d-none">
                    @csrf
                    <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png">
                </form>
            </div>

            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>

            <div class="d-flex justify-content-center mb-3">
                <span class="cms-badge cms-badge-purple">
                    <i class="bi bi-{{ $roleIcon }}"></i>
                    {{ $roleName }}
                </span>
            </div>

            <div class="profile-meta">
                @if($isDoctor)
                <div class="profile-meta-item">
                    <span class="meta-val">{{ $levelCount }}</span>
                    <span class="meta-lbl">{{ __('cms.profile.requested_levels_label') }}</span>
                </div>
                @endif
                <div class="profile-meta-item">
                    <span class="meta-val">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:1rem;"></i>
                    </span>
                    <span class="meta-lbl">{{ $roleName }}</span>
                </div>
                <div class="profile-meta-item">
                    <span class="meta-val">
                        <i class="bi bi-shield-check" style="font-size:1rem;color:#a78bfa;"></i>
                    </span>
                    <span class="meta-lbl">Secure</span>
                </div>
            </div>
        </div>

        {{-- Quick Info Card --}}
        <div class="profile-info-card">
            <div class="profile-info-card-header">
                <i class="bi bi-info-circle"></i>
                Account Details
            </div>

            <div class="profile-info-row">
                <div class="info-icon"><i class="bi bi-person-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">{{ __('cms.doctors.name') }}</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
            </div>

            <div class="profile-info-row">
                <div class="info-icon"><i class="bi bi-envelope-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">{{ __('cms.auth.email') }}</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
            </div>

            @if($isDoctor && $user->phone)
            <div class="profile-info-row">
                <div class="info-icon"><i class="bi bi-telephone-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">{{ __('cms.auth.phone_optional') }}</span>
                    <span class="info-value">{{ $user->phone }}</span>
                </div>
            </div>
            @endif

            <div class="profile-info-row">
                <div class="info-icon"><i class="bi bi-award-fill"></i></div>
                <div class="info-content">
                    <span class="info-label">{{ __('cms.profile.role_label') }}</span>
                    <span class="info-value">{{ $roleName }}</span>
                </div>
            </div>
        </div>

    </div>{{-- /profile-left --}}

    {{-- ══════════════════════════════════════════
         RIGHT PANEL
    ══════════════════════════════════════════ --}}
    <div class="profile-right">

        {{-- Pill Tabs --}}
        <div class="profile-tabs" role="tablist">
            <button class="profile-tab-btn active" id="tab-info"
                    data-bs-toggle="tab" data-bs-target="#panel-info"
                    type="button" role="tab" aria-selected="true">
                <i class="bi bi-person-lines-fill"></i>
                {{ __('cms.profile.tab_info') }}
            </button>
            <button class="profile-tab-btn" id="tab-password"
                    data-bs-toggle="tab" data-bs-target="#panel-password"
                    type="button" role="tab" aria-selected="false">
                <i class="bi bi-shield-lock-fill"></i>
                {{ __('cms.profile.tab_password') }}
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="tab-content" id="profileTabsContent" style="flex:1;">

            {{-- ── TAB 1: Personal Info ─────────────────────────────── --}}
            <div class="tab-pane fade show active profile-panel" id="panel-info"
                 role="tabpanel" aria-labelledby="tab-info">

                <div class="profile-section-label">
                    <i class="bi bi-person-vcard-fill"></i>
                    {{ __('cms.profile.tab_info') }}
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-12 col-md-6">
                            <div class="profile-input-group">
                                <label for="profileName">{{ __('cms.doctors.name') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-person field-icon"></i>
                                    <input type="text" id="profileName" name="name"
                                           class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                                           value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-12 col-md-6">
                            <div class="profile-input-group">
                                <label for="profileEmail">{{ __('cms.auth.email') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-envelope field-icon"></i>
                                    <input type="email" id="profileEmail" name="email"
                                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                                           value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Phone (Doctor only) --}}
                        @if($isDoctor)
                        <div class="col-12 col-md-6">
                            <div class="profile-input-group">
                                <label for="profilePhone">{{ __('cms.auth.phone_optional') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-telephone field-icon"></i>
                                    <input type="text" id="profilePhone" name="phone"
                                           class="{{ $errors->has('phone') ? 'is-invalid' : '' }}"
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="01XXXXXXXXX" maxlength="11">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        {{-- Role (read-only) --}}
                        <div class="col-12 col-md-6">
                            <div class="profile-input-group">
                                <label>{{ __('cms.profile.role_label') }}</label>
                                <div class="profile-locked-field">
                                    <i class="bi bi-lock-fill lock-icon"></i>
                                    <span>{{ $roleName }}</span>
                                    <span class="cms-badge cms-badge-muted ms-auto" style="font-size:0.62rem;">{{ __('cms.profile.read_only') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Requested Levels (Doctor only) --}}
                        @if($isDoctor)
                        <div class="col-12">
                            <div class="profile-input-group">
                                <label>{{ __('cms.profile.requested_levels_label') }}</label>
                                <div class="profile-levels-area">
                                    @forelse($levels as $lvl)
                                        <span class="cms-badge cms-badge-purple">
                                            <i class="bi bi-bookmark-fill"></i> {{ $lvl }}
                                        </span>
                                    @empty
                                        <span style="font-size:0.8rem;color:var(--text-faint);">
                                            <i class="bi bi-dash-circle me-1"></i>{{ __('cms.profile.no_requested_levels') }}
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <button type="submit" class="profile-save-btn">
                            <i class="bi bi-floppy2-fill"></i>
                            {{ __('cms.profile.save_info') }}
                        </button>
                        <span style="font-size:0.75rem;color:var(--text-faint);">
                            <i class="bi bi-shield-check me-1"></i>Changes saved securely
                        </span>
                    </div>
                </form>

            </div>

            {{-- ── TAB 2: Change Password ───────────────────────────── --}}
            <div class="tab-pane fade profile-panel" id="panel-password"
                 role="tabpanel" aria-labelledby="tab-password">

                <div class="profile-section-label">
                    <i class="bi bi-shield-lock-fill"></i>
                    {{ __('cms.profile.tab_password') }}
                </div>

                {{-- Security tips --}}
                <div class="cms-alert cms-alert-info d-flex align-items-center gap-2 mb-4"
                     style="border-radius:10px;font-size:0.82rem;">
                    <i class="bi bi-lightbulb-fill flex-shrink-0"></i>
                    <span>Use at least 8 characters including letters and numbers for a strong password.</span>
                </div>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Current Password --}}
                        <div class="col-12">
                            <div class="profile-input-group">
                                <label for="currentPassword">{{ __('cms.profile.current_password') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-key-fill field-icon"></i>
                                    <input type="password" id="currentPassword" name="current_password"
                                           class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                                           required autocomplete="current-password">
                                    <button type="button" class="profile-eye-btn" data-target="currentPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- New Password --}}
                        <div class="col-12 col-md-6">
                            <div class="profile-input-group">
                                <label for="newPassword">{{ __('cms.profile.new_password') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-lock-fill field-icon"></i>
                                    <input type="password" id="newPassword" name="password"
                                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                                           minlength="8" required autocomplete="new-password"
                                           oninput="checkPwStrength(this.value)">
                                    <button type="button" class="profile-eye-btn" data-target="newPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                {{-- Strength meter --}}
                                <div class="pw-strength" id="pwStrengthBars">
                                    <div class="pw-strength-bar" id="bar1"></div>
                                    <div class="pw-strength-bar" id="bar2"></div>
                                    <div class="pw-strength-bar" id="bar3"></div>
                                    <div class="pw-strength-bar" id="bar4"></div>
                                </div>
                                <div class="pw-strength-label" id="pwStrengthLabel"></div>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-12 col-md-6">
                            <div class="profile-input-group">
                                <label for="confirmPassword">{{ __('cms.profile.confirm_password') }}</label>
                                <div class="profile-input-wrap">
                                    <i class="bi bi-lock-fill field-icon"></i>
                                    <input type="password" id="confirmPassword" name="password_confirmation"
                                           minlength="8" required autocomplete="new-password">
                                    <button type="button" class="profile-eye-btn" data-target="confirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <button type="submit" class="profile-save-btn">
                            <i class="bi bi-shield-check"></i>
                            {{ __('cms.profile.save_password') }}
                        </button>
                    </div>
                </form>

            </div>

        </div>{{-- /tab-content --}}
    </div>{{-- /profile-right --}}

</div>{{-- /profile-shell --}}
</div>

@push('scripts')
<script>
(function () {
    // ── Avatar upload ──────────────────────────────────────
    const btn   = document.getElementById('triggerAvatarUpload');
    const input = document.getElementById('avatarInput');
    const form  = document.getElementById('avatarForm');
    if (btn && input && form) {
        btn.addEventListener('click', () => input.click());
        input.addEventListener('change', () => { if (input.files.length > 0) form.submit(); });
    }

    // ── Eye toggle for password fields ────────────────────
    document.querySelectorAll('.profile-eye-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input    = document.getElementById(targetId);
            const icon     = this.querySelector('i');
            if (!input) return;
            const isPass = input.type === 'password';
            input.type   = isPass ? 'text' : 'password';
            icon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // ── Auto-switch tab on password errors ────────────────
    @if($errors->has('current_password') || $errors->has('password'))
        const pwTab = document.getElementById('tab-password');
        if (pwTab) new bootstrap.Tab(pwTab).show();
    @endif
})();

// ── Password strength meter ────────────────────────────────
function checkPwStrength(val) {
    const bars   = [document.getElementById('bar1'), document.getElementById('bar2'),
                    document.getElementById('bar3'), document.getElementById('bar4')];
    const label  = document.getElementById('pwStrengthLabel');
    bars.forEach(b => { if(b) b.className = 'pw-strength-bar'; });
    if (!val || val.length < 1) { if(label) label.textContent=''; return; }

    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;

    const classes = ['weak','weak','medium','strong'];
    const labels  = ['Weak','Fair','Good','Strong 💪'];
    const colors  = ['weak','weak','medium','strong'];

    for (let i = 0; i < Math.max(score, 1); i++) {
        if (bars[i]) bars[i].classList.add(colors[score - 1] || 'weak');
    }
    if (label) label.textContent = labels[score - 1] || 'Too short';
}
</script>
@endpush
@endsection
