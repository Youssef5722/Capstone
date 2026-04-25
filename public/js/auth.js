document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Password Show/Hide Toggle
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // 2. Client-Side Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            let isValid = true;
            
            // Clear previous errors
            form.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('is-invalid');
            });

            // Required fields
            const requiredFields = form.querySelectorAll('input[required]');
            requiredFields.forEach(input => {
                if (!input.value.trim()) {
                    setInvalid(input, 'This field is required.');
                    isValid = false;
                }
            });

            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(input => {
                if (input.value.trim() && !validateEmail(input.value)) {
                    setInvalid(input, 'Please enter a valid email address.');
                    isValid = false;
                }
            });

            // Password length (min 8)
            const passwordFields = form.querySelectorAll('input[type="password"]');
            passwordFields.forEach(input => {
                if (input.value.trim() && input.value.length < 8) {
                    setInvalid(input, 'Password must be at least 8 characters long.');
                    isValid = false;
                }
            });

            // Cross-field confirm password check
            const passwordInputs = Array.from(passwordFields);
            const mainPassword = passwordInputs.find(i => i.name === 'password' || i.id.includes('password'));
            const confirmPassword = passwordInputs.find(i => i.name === 'confirm_password' || i.id.includes('confirm_password'));
            
            if (mainPassword && confirmPassword) {
                if (mainPassword.value !== confirmPassword.value) {
                    setInvalid(confirmPassword, 'Passwords do not match.');
                    isValid = false;
                }
            }

            if (isValid) {
                // Remove visual errors and ideally submit the form via AJAX or form.submit()
                console.log("Form is valid. Ready for backend submission.");
                form.submit();
            }
        }, false);
    });

    function setInvalid(input, message) {
        input.classList.add('is-invalid');
        // Find adjacent invalid-feedback and update message
        let feedback = input.parentElement.parentElement.querySelector('.invalid-feedback');
        if(!feedback) {
            feedback = input.parentElement.querySelector('.invalid-feedback');
        }
        if (feedback) {
            feedback.textContent = message;
        }
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
});

// =============================================
// PANEL SWITCHER — Smooth In-Card Transition
// Activates only on pages with #formPanelsWrapper
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    var wrapper = document.getElementById('formPanelsWrapper');
    if (!wrapper) return; // Not a combined auth page — exit silently

    // ── Tab clicks ────────────────────────────────────────────────────────────
    document.querySelectorAll('.auth-tab[data-panel]').forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            switchPanel(this.dataset.panel);
        });
    });

    // ── Switch-panel-link clicks (delegated, handles dynamically added links) ─
    document.addEventListener('click', function (e) {
        var link = e.target.closest('.switch-panel-link');
        if (link) {
            e.preventDefault();
            switchPanel(link.dataset.panel);
        }
    });

    // ── Core switch function ──────────────────────────────────────────────────
    function switchPanel(targetId) {
        if (wrapper.dataset.transitioning === 'true') return;

        var current = wrapper.querySelector('.form-panel.panel-visible');
        var target  = document.getElementById(targetId);
        if (!current || !target || current === target) return;

        wrapper.dataset.transitioning = 'true';

        // 1. Lock wrapper height to avoid collapse during transition
        wrapper.style.height = current.offsetHeight + 'px';

        // 2. Measure the target panel's natural (auto) height.
        //    We temporarily put it back in flow (panel-entering) but
        //    keep it visually invisible, then read offsetHeight.
        target.classList.remove('panel-hidden');
        target.classList.add('panel-entering');
        var targetHeight = target.offsetHeight;
        target.classList.remove('panel-entering');
        target.classList.add('panel-hidden'); // restore — will be removed below

        // 3. Start the exit animation on the current panel
        current.classList.remove('panel-visible');
        current.classList.add('panel-exiting');

        // 4. Animate wrapper height toward the target panel's height
        requestAnimationFrame(function () {
            wrapper.style.height = targetHeight + 'px';
        });

        // 5. After the exit animation finishes, bring the new panel in
        setTimeout(function () {

            // Hide the old panel fully
            current.classList.remove('panel-exiting');
            current.classList.add('panel-hidden');

            // Put the new panel in flow (at starting offset, invisible)
            target.classList.remove('panel-hidden');
            target.classList.add('panel-entering');

            // Double rAF: first call triggers a layout pass so the browser
            // registers the entering state; second call applies panel-visible
            // which kicks off the CSS opacity + transform transition.
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    target.classList.remove('panel-entering');
                    target.classList.add('panel-visible');
                });
            });

            // ── Sync active tab styling ───────────────────────────────────────
            var themeClass = document.body.classList.contains('doctor-theme')
                             ? 'doctor-theme' : 'student-theme';
            document.querySelectorAll('.auth-tab[data-panel]').forEach(function (tab) {
                var isActive = tab.dataset.panel === targetId;
                tab.classList.toggle('active', isActive);
                if (isActive)   tab.classList.add(themeClass);
                else            tab.classList.remove('doctor-theme', 'student-theme');
            });

            // ── Swap card header from the panel's data attributes ─────────────
            var titleEl    = document.getElementById('cardTitle');
            var subtitleEl = document.getElementById('cardSubtitle');
            if (titleEl && target.dataset.titleHtml !== undefined) {
                titleEl.innerHTML  = target.dataset.titleHtml;
                titleEl.className  = target.dataset.titleClass || 'auth-card-title';
            }
            if (subtitleEl && target.dataset.subtitle) {
                subtitleEl.textContent = target.dataset.subtitle;
            }

            // ── Update browser tab title ──────────────────────────────────────
            if (target.dataset.pageTitle) {
                document.title = target.dataset.pageTitle;
            }

            // ── Release the height lock once the enter animation settles ──────
            setTimeout(function () {
                wrapper.style.height = '';
                wrapper.dataset.transitioning = 'false';
            }, 430);

        }, 240); // matches panel-exiting transition (0.22s + tiny buffer)
    }
});
