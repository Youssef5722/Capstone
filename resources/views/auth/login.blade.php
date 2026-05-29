@extends('layouts.auth')

@section('title', __('cms.auth.doctor_portal') . ' — ' . __('cms.auth.tab_login') . ' — CMS')
@section('theme_class', 'doctor-theme')

@section('content')


@php
    // Pre-compute translated strings for JS data-* attributes (must be HTML-safe)
    $jsWelcomeBack   = e(__('cms.auth.welcome_back'));
    $jsSignInDoctor  = e(__('cms.auth.sign_in_doctor'));
    $jsFillDetails   = e(__('cms.auth.fill_details'));
    $jsDocRegTitle   = e(__('cms.auth.doc_reg_title'));
@endphp

<div class="auth-wrapper">

    <!-- Left Branding Area -->
    <div class="auth-sidebar">
        <div class="brand-tag doctor"> {{ strtoupper(__('cms.auth.doctor_portal')) }}</div>
        <h1 class="auth-heading">{{ __('cms.auth.heading_line1') }} <br><span class="text-gradient-doctor">{{ __('cms.auth.heading_gradient') }}</span><br>{{ __('cms.auth.heading_line3') }}</h1>
        <p class="auth-subheading">{{ __('cms.auth.doctor_subheading') }}</p>
    </div>

    <!-- Right Form Area -->
    <div class="auth-form-container">
        <div class="auth-card large" id="doctorAuthCard">

            <div class="auth-card-header">
                <h2 class="auth-card-title" id="cardTitle">{{ __('cms.auth.welcome_back') }}</h2>
                <p class="auth-card-subtitle" id="cardSubtitle">{{ __('cms.auth.sign_in_doctor') }}</p>
                @if(session('success'))
                    <div class="alert alert-success mt-3" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab active doctor-theme" data-panel="panelLogin">{{ __('cms.auth.tab_login') }}</a>
                <a href="{{ route('register') }}" class="auth-tab" data-panel="panelRegister">{{ __('cms.auth.tab_signup') }}</a>
            </div>

            <div class="form-panels-wrapper" id="formPanelsWrapper">

                <!-- PANEL: LOGIN (Visible) -->
                <div class="form-panel panel-visible" id="panelLogin"
                     data-title-html="{{ $jsWelcomeBack }}"
                     data-title-class="auth-card-title"
                     data-subtitle="{{ $jsSignInDoctor }}"
                     data-page-title="{{ __('cms.auth.doctor_portal') }} — {{ __('cms.auth.tab_login') }} — CMS">

                    <form class="needs-validation" method="POST" action="{{ route('login') }}" novalidate id="loginForm">
                        @csrf
                        <div class="mb-4 position-relative">
                            <label class="form-label" for="loginEmail">{{ __('cms.auth.email_label') }}</label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-envelope prefix-icon"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="loginEmail"
                                       placeholder="{{ __('cms.auth.email_placeholder') }}" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">{{ __('cms.auth.email_label') }}</div>
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
                                <a href="{{ route('password.request') }}" class="btn-forgot">{{ __('cms.auth.forgot_password') }}</a>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">{{ __('cms.auth.password_label') }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="custom-checkbox">
                                <input type="checkbox" name="remember">
                                <span>{{ __('cms.auth.remember_me') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-primary-doctor">
                            {{ __('cms.auth.sign_in_btn') }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-2"></i>
                        </button>
                    </form>

                    <div class="auth-footer-text">
                        {{ __('cms.auth.no_account') }}
                        <a href="{{ route('register') }}" class="switch-panel-link" data-panel="panelRegister">{{ __('cms.auth.register_here') }}</a>
                    </div>
                </div>

                <!-- PANEL: REGISTER (Hidden) -->
                <div class="form-panel panel-hidden" id="panelRegister"
                     data-title-html='<i class="bi bi-circle-fill" style="font-size:8px;vertical-align:middle;"></i> {{ strtoupper($jsDocRegTitle) }}'
                     data-title-class="auth-card-title register-title doctor"
                     data-subtitle="{{ $jsFillDetails }}"
                     data-page-title="{{ __('cms.auth.doctor_portal') }} — {{ __('cms.auth.tab_signup') }} — CMS">

                    <div class="alert alert-info"
                         style="background:rgba(10,255,255,0.1);border:1px solid rgba(122,34,253,0.3);color:#fff;font-size:0.85rem;margin-bottom:1.5rem;">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('cms.auth.pending_notice') }}
                    </div>

                    <form class="needs-validation" method="POST" action="{{ route('register') }}" novalidate id="registerForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="regFullName">{{ __('cms.auth.full_name') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-person prefix-icon"></i>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="regFullName"
                                           placeholder="{{ __('cms.ui.doctor_name_placeholder') }}" value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.full_name') }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="regEmail">{{ __('cms.auth.email_label') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-envelope prefix-icon"></i>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="regEmail"
                                           placeholder="{{ __('cms.ui.doctor_email_placeholder') }}" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.email_label') }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="regNationalId">{{ __('cms.auth.national_id') }}
                                    <span style="font-size:.75rem;opacity:.6;font-weight:400 ;">{{ __('cms.auth.national_id_hint') }}</span>
                                </label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-person-vcard prefix-icon"></i>
                                    <input type="text"
                                           class="form-control @error('national_id') is-invalid @enderror"
                                           name="national_id" id="regNationalId"
                                           placeholder="{{ __('cms.ui.national_id_placeholder') }}"
                                           value="{{ old('national_id') }}"
                                           maxlength="14" minlength="14"
                                           inputmode="numeric"
                                           pattern="[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{7}"
                                           required>
                                </div>
                                @error('national_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.national_id') }}</div>
                                @enderror
                                <div class="form-text" style="font-size:.75rem;opacity:.55;margin-top:.3rem;">
                                    {{ __('cms.auth.national_id_help') }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="regPhone">{{ __('cms.auth.phone_optional') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-telephone prefix-icon"></i>
                                    <input type="text" class="form-control" name="phone" id="regPhone"
                                           placeholder="+1 (555) 000-0000" value="{{ old('phone') }}">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="regPassword">{{ __('cms.auth.password_label') }}
                                    <span style="font-size:.75rem;opacity:.6;font-weight:400;">{{ __('cms.auth.password_hint') }}</span>
                                </label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-lock prefix-icon"></i>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" id="regPassword"
                                           placeholder="••••••••" minlength="8" maxlength="72" required>
                                    <button type="button" class="btn-toggle-password" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="pwStrengthBar" style="height:3px;border-radius:2px;margin-top:.4rem;transition:all .3s;background:#333;">
                                    <div id="pwStrengthFill" style="height:100%;width:0%;border-radius:2px;transition:all .3s;"></div>
                                </div>
                                <div id="pwStrengthLabel" style="font-size:.7rem;opacity:.6;margin-top:.2rem;"></div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.password_hint') }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 position-relative">
                                <label class="form-label" for="regConfirmPassword">{{ __('cms.auth.confirm_password') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-lock prefix-icon"></i>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                           name="password_confirmation"
                                           id="regConfirmPassword" placeholder="••••••••" required>
                                    <button type="button" class="btn-toggle-password" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="pwMatchLabel" style="font-size:.7rem;margin-top:.25rem;"></div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">{{ __('cms.auth.confirm_password') }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Preferred Levels (optional) --}}
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('cms.doctors.preferred_levels') }}
                                <span style="font-size:.75rem;opacity:.6;font-weight:400;">
                                    ({{ __('cms.doctors.preferred_levels_hint') }})
                                </span>
                            </label>

                            @php $levels = \App\Models\Level::orderBy('name')->get(); @endphp

                            @if($levels->isEmpty())
                                <p class="text-muted" style="font-size: 0.8rem; margin: 0;">{{ __('cms.assignments.no_levels') ?? 'No levels available' }}</p>
                            @else
                                <div class="radio-pill-group">
                                    @foreach($levels as $level)
                                    <div class="radio-pill">
                                        <input
                                            type="checkbox"
                                            name="requested_levels[]"
                                            value="{{ $level->id }}"
                                            id="login_level_{{ $level->id }}"
                                            {{ in_array($level->id, old('requested_levels', [])) ? 'checked' : '' }}
                                        >
                                        <label for="login_level_{{ $level->id }}">
                                            {{ $level->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            @endif

                            @error('requested_levels')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn-primary-doctor mt-2">
                            {{ __('cms.auth.create_account_btn') }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-2"></i>
                        </button>
                    </form>

                    <div class="auth-footer-text" style="font-size: 0.8rem;">
                        {{ __('cms.auth.already_account') }}
                        <a href="{{ route('login') }}" class="switch-panel-link" data-panel="panelLogin">{{ __('cms.auth.sign_in_here') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /* ── National ID live validation ─────────────────────────────── */
    const nidInput  = document.getElementById('regNationalId');
    const NID_RE    = /^[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{7}$/;

    if (nidInput) {
        nidInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 14);
        });
        nidInput.addEventListener('blur', function () {
            const ok = NID_RE.test(this.value);
            this.classList.toggle('is-invalid', !ok && this.value.length > 0);
            this.classList.toggle('is-valid',    ok);
        });
    }

    /* ── Password strength meter ─────────────────────────────────── */
    const pwInput  = document.getElementById('regPassword');
    const bar      = document.getElementById('pwStrengthFill');
    const barLabel = document.getElementById('pwStrengthLabel');

    function scorePassword(pw) {
        let score = 0;
        if (pw.length >= 8)  score++;
        if (pw.length >= 12) score++;
        if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
        if (/\d/.test(pw))   score++;
        if (/[^a-zA-Z0-9]/.test(pw)) score++;
        return score;
    }

    const levels = [
        { pct: '0%',   color: '#333',    label: '' },
        { pct: '25%',  color: '#ef4444', label: 'Weak' },
        { pct: '50%',  color: '#f97316', label: 'Fair' },
        { pct: '75%',  color: '#eab308', label: 'Good' },
        { pct: '100%', color: '#22c55e', label: 'Strong' },
        { pct: '100%', color: '#10b981', label: 'Very Strong' },
    ];

    if (pwInput && bar) {
        pwInput.addEventListener('input', function () {
            const score = this.value.length ? scorePassword(this.value) : 0;
            const lvl   = levels[score] || levels[0];
            bar.style.width      = lvl.pct;
            bar.style.background = lvl.color;
            if (barLabel) { barLabel.textContent = lvl.label; barLabel.style.color = lvl.color; }
        });
    }

    /* ── Confirm password match indicator ───────────────────────── */
    const pwConfirm = document.getElementById('regConfirmPassword');
    const matchLbl  = document.getElementById('pwMatchLabel');

    function checkMatch() {
        if (!pwInput || !pwConfirm || !matchLbl) return;
        if (!pwConfirm.value) { matchLbl.textContent = ''; return; }
        const match = pwInput.value === pwConfirm.value;
        matchLbl.textContent = match ? '✓ Passwords match' : '✗ Passwords do not match';
        matchLbl.style.color = match ? '#22c55e' : '#ef4444';
        pwConfirm.classList.toggle('is-invalid', !match);
        pwConfirm.classList.toggle('is-valid',    match);
    }

    if (pwConfirm) {
        pwConfirm.addEventListener('input', checkMatch);
        if (pwInput) pwInput.addEventListener('input', checkMatch);
    }

    /* ── Block form submit if National ID invalid ────────────────── */
    const regForm = document.getElementById('registerForm');
    if (regForm && nidInput) {
        regForm.addEventListener('submit', function (e) {
            if (!NID_RE.test(nidInput.value)) {
                e.preventDefault();
                nidInput.classList.add('is-invalid');
                nidInput.focus();
            }
        });
    }
})();
</script>
@endpush
