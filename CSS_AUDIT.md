# CSS Audit Report — Capstone Management System
**Generated:** 2026-05-28  
**Scope:** `resources/views/**/*.blade.php` and `public/css/`  
**Method:** Manual audit (read-only — no files were modified)

---

## 1. Files in `public/css/`

| File | Size | Lines | Description |
|------|------|-------|-------------|
| `public/css/auth.css` | 15,115 bytes | 551 | Auth layout design system (login, register, welcome pages) |
| `public/css/dashboard.css` | 34,210 bytes | 1,419 | Dashboard design system (nav, sidebar, cards, tables, buttons, badges, alerts, pagination) |

---

## 2. Blade Files WITH `<style>` Blocks

Seven (7) Blade files contain inline `<style>` blocks. All are listed below with their selectors and approximate line counts.

---

### 2.1 `resources/views/auth/login.blade.php`

**`<style>` block location:** Lines 8–64  
**Approximate line count:** 57 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.radio-pill-group` | Flex container for pill-style checkbox group |
| `.radio-pill` | Individual pill wrapper (flex: 1, min-width: 120px) |
| `.radio-pill input[type="checkbox"]` | Hidden checkbox (opacity: 0) |
| `.radio-pill label` | Styled pill label (border, border-radius, transition) |
| `.radio-pill label::before` | Small dot indicator (6×6px circle) |
| `.radio-pill input[type="checkbox"]:checked + label` | Active pill state (border-color: #8b5cf6) |
| `.radio-pill input[type="checkbox"]:checked + label::before` | Active dot (background: #8b5cf6, glow) |
| `.radio-pill input[type="checkbox"]:focus-visible + label` | Focus ring (outline: 2px solid #8b5cf6) |

> **Note:** This style block is a **duplicate** of the one in `register.blade.php` (identical content).

---

### 2.2 `resources/views/auth/register.blade.php`

**`<style>` block location:** Lines 7–63  
**Approximate line count:** 57 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.radio-pill-group` | Flex container for pill-style checkbox group |
| `.radio-pill` | Individual pill wrapper (flex: 1, min-width: 120px) |
| `.radio-pill input[type="checkbox"]` | Hidden checkbox (opacity: 0) |
| `.radio-pill label` | Styled pill label (border, border-radius, transition) |
| `.radio-pill label::before` | Small dot indicator (6×6px circle) |
| `.radio-pill input[type="checkbox"]:checked + label` | Active pill state (border-color: #8b5cf6) |
| `.radio-pill input[type="checkbox"]:checked + label::before` | Active dot (background: #8b5cf6, glow) |
| `.radio-pill input[type="checkbox"]:focus-visible + label` | Focus ring (outline: 2px solid #8b5cf6) |

> **Note:** Identical duplicate of the style block in `login.blade.php`.

---

### 2.3 `resources/views/doctor/dashboard.blade.php`

**`<style>` block location:** Lines 6–480 (inside `@push('styles')`)  
**Approximate line count:** 475 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.doc-hero` | Doctor dashboard hero/welcome banner (gradient background, border-radius: 20px) |
| `.doc-hero::before` | Hero decorative circle (top-right radial gradient) |
| `.doc-hero::after` | Hero decorative circle (bottom-left radial gradient) |
| `.doc-hero-content` | Hero left content (z-index: 1) |
| `.doc-hero-greeting` | Greeting label (font-size: 0.78rem, uppercase, letter-spacing) |
| `.doc-hero h1` | Hero heading (font-size: 2rem) |
| `.doc-hero h1 span` | Gradient text (linear-gradient #a78bfa → #0AFFFF) |
| `.doc-hero-sub` | Hero subtitle (font-size: 0.9rem) |
| `.doc-hero-meta` | Hero right metadata column |
| `.doc-stats-grid` | KPI stats grid (auto-fit, minmax 180px) |
| `.doc-stat-card` | Individual KPI stat card (border-radius: 16px, transition) |
| `.doc-stat-card::before` | Top accent bar (2px height, initially opacity: 0) |
| `.doc-stat-card.purple::before` | Purple gradient top bar |
| `.doc-stat-card.cyan::before` | Cyan gradient top bar |
| `.doc-stat-card.green::before` | Green gradient top bar |
| `.doc-stat-card.amber::before` | Amber gradient top bar |
| `.doc-stat-card:hover` | Stat card hover (translateY(-4px), box-shadow) |
| `.doc-stat-card:hover::before` | Reveals top bar on hover |
| `.doc-stat-icon` | Stat card icon box (50×50px, border-radius: 12px) |
| `.doc-stat-icon.purple` | Purple icon background/color |
| `.doc-stat-icon.cyan` | Cyan icon background/color |
| `.doc-stat-icon.green` | Green icon background/color |
| `.doc-stat-icon.amber` | Amber icon background/color |
| `.doc-stat-body` | Stat text wrapper (empty rule) |
| `.doc-stat-value` | Stat number (font-size: 2rem, font-weight: 700) |
| `.doc-stat-label` | Stat description (font-size: 0.78rem) |
| `.doc-section-header` | Section title row (flex, space-between) |
| `.doc-section-title` | Section title text |
| `.doc-section-title i` | Icon in section title (color: #a78bfa) |
| `.doc-level-card` | Level card container (border-radius: 16px, flex column) |
| `.doc-level-card:hover` | Level card hover (translateY(-5px), glow shadow) |
| `.doc-level-card-top` | Level card top region (gradient background) |
| `.doc-level-card-top::after` | Decorative circle in card top |
| `.doc-level-name` | Level name text (font-size: 1.15rem) |
| `.doc-level-name i` | Level name icon (color: #a78bfa) |
| `.doc-level-card-body` | Level card body (padding, flex column, gap) |
| `.doc-level-stat-row` | Individual stat row inside level card |
| `.doc-level-stat-row .label` | Stat row label (flex, icon + text) |
| `.doc-level-stat-row .label i` | Label icon |
| `.doc-level-stat-row .value` | Stat value (font-weight: 700) |
| `.doc-level-card-footer` | Level card footer (action buttons area) |
| `.doc-quick-actions` | Quick actions grid (auto-fit, minmax 200px) |
| `.doc-qa-card` | Quick action card (flex, border-radius: 12px) |
| `.doc-qa-card::before` | Quick action card overlay gradient (initially hidden) |
| `.doc-qa-card.purple::before` | Purple overlay gradient |
| `.doc-qa-card.cyan::before` | Cyan overlay gradient |
| `.doc-qa-card.green::before` | Green overlay gradient |
| `.doc-qa-card.amber::before` | Amber overlay gradient |
| `.doc-qa-card:hover` | QA card hover (translateY(-2px), shadow) |
| `.doc-qa-card.purple:hover` | Purple border on hover |
| `.doc-qa-card.cyan:hover` | Cyan border on hover |
| `.doc-qa-card.green:hover` | Green border on hover |
| `.doc-qa-card.amber:hover` | Amber border on hover |
| `.doc-qa-card:hover::before` | Reveals overlay on hover |
| `.doc-qa-icon` | QA card icon (40×40px, border-radius: 10px) |
| `.doc-qa-icon.purple` | Purple icon variant |
| `.doc-qa-icon.cyan` | Cyan icon variant |
| `.doc-qa-icon.green` | Green icon variant |
| `.doc-qa-icon.amber` | Amber icon variant |
| `.doc-qa-text` | QA card text block |
| `.doc-qa-text .qa-label` | QA card label text |
| `.doc-qa-text .qa-sub` | QA card subtitle text |
| `.doc-qa-arrow` | Chevron arrow icon |
| `.doc-qa-card:hover .doc-qa-arrow` | Arrow shifts right on hover |
| `.doc-empty-hero` | Empty state container (centered, flex column) |
| `.doc-empty-hero .empty-icon-wrap` | Empty state icon circle (80×80px, animated) |
| `@keyframes float-bob` | Float animation for empty icon (0% → -8px → 0%) |
| `.doc-empty-hero h4` | Empty state heading |
| `.doc-empty-hero p` | Empty state description |
| `.counter-value` | Animated counter wrapper (display: inline-block) |
| `[dir="rtl"] .doc-hero-meta` | RTL: align-items flex-start |
| `[dir="rtl"] .doc-qa-arrow` | RTL: flip arrow direction (scaleX(-1)) |
| `[dir="rtl"] .doc-qa-card:hover .doc-qa-arrow` | RTL: flip + translate on hover |
| `@media (max-width: 768px) .doc-hero` | Mobile hero: column direction |
| `@media (max-width: 768px) .doc-hero h1` | Mobile hero heading smaller |
| `@media (max-width: 768px) .doc-hero-meta` | Mobile meta alignment |
| `@media (max-width: 768px) .doc-stats-grid` | Mobile: 2-column stats |
| `@media (max-width: 768px) .doc-quick-actions` | Mobile: single column quick actions |
| `@media (max-width: 480px) .doc-stats-grid` | Small mobile: 1×1 stats |
| `@media (max-width: 480px) .doc-stat-value` | Small mobile: smaller stat number |

---

### 2.4 `resources/views/doctor/teams/create.blade.php`

**`<style>` block location:** Lines 126–151 (inside `@push('styles')`)  
**Approximate line count:** 26 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.custom-scroll::-webkit-scrollbar` | Custom scrollbar width (6px) for members list |
| `.custom-scroll::-webkit-scrollbar-track` | Transparent scrollbar track |
| `.custom-scroll::-webkit-scrollbar-thumb` | Scrollbar thumb (border-radius: 10px, border-color) |
| `.custom-scroll::-webkit-scrollbar-thumb:hover` | Hover state (color: #a78bfa) |
| `.custom-checkbox .form-check-input` | Checkbox transition animation |
| `.custom-checkbox .form-check-input:checked` | Checked checkbox (background: #7A22FD, scale: 1.1) |

---

### 2.5 `resources/views/doctor/teams/distribute.blade.php`

**`<style>` block location:** Lines 144–223 (inside `@push('styles')`)  
**Approximate line count:** 80 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.doc-stat-card:hover` | Stat card hover override (translateY(-5px), box-shadow, border-color) |
| `.mode-card` | Distribution mode selection card (border: 2px, border-radius: 12px, transition) |
| `.mode-card:hover` | Mode card hover state (border-color, background) |
| `.mode-icon` | Mode icon box (32×32px, border-radius: 8px) |
| `.mode-card span.fw-bold` | Mode card label (color, font-size: 1.05rem) |
| `.mode-hint` | Mode hint text (color: var(--text-faint), font-size: 0.85rem) |
| `.check-indicator` | Check icon (color: #a78bfa, initially opacity: 0, scale: 0.5) |
| `.btn-check:checked + .mode-card` | Checked mode card (border-color: #7A22FD, gradient background) |
| `.btn-check:checked + .mode-card .mode-icon` | Checked mode icon (background: #7A22FD, color: #fff) |
| `.btn-check:checked + .mode-card .check-indicator` | Checked indicator visible (opacity: 1, scale: 1) |
| `.btn-check:checked + .mode-card .mode-hint` | Checked hint color (var(--text-muted)) |

---

### 2.6 `resources/views/doctor/workspaces/index.blade.php`

**`<style>` block location:** Lines 6–103 (inside `@push('styles')`)  
**Approximate line count:** 98 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.ws-card` | Workspace card (flex column, border-radius: 16px, transition) |
| `.ws-card::before` | Top accent gradient bar (initially opacity: 0) |
| `.ws-card:hover` | Workspace card hover (translateY(-4px), purple glow) |
| `.ws-card:hover::before` | Reveals accent bar on hover |
| `.ws-team-label` | Team label micro-text (0.72rem, uppercase, faint color) |
| `.ws-team-name` | Team name heading (1.05rem, font-weight: 700) |
| `.ws-team-name i` | Team name icon (color: #a78bfa) |
| `.ws-project` | Project name row (0.82rem, flex, icon + text) |
| `.ws-project i` | Project icon (color: #fbbf24) |
| `.ws-stats` | Stats row (flex, gap: 1.25rem) |
| `.ws-stats span` | Individual stat item (flex, icon + text) |
| `.ws-progress-label` | Progress bar header (space-between, 0.72rem) |
| `.ws-progress-bar` | Progress bar track (background: rgba, border-radius: 999px, height: 6px) |
| `.ws-progress-fill` | Progress bar fill (gradient #a78bfa → #0AFFFF, transition) |

---

### 2.7 `resources/views/student/dashboard.blade.php`

**`<style>` block location:** Lines 6–265 (inside `@push('styles')`)  
**Approximate line count:** 260 lines

| Selector / Rule | Description |
|-----------------|-------------|
| `.stu-hero` | Student dashboard hero banner (cyan gradient, border-radius: 20px) |
| `.stu-hero::before` | Hero top-right decorative circle (cyan radial gradient) |
| `.stu-hero::after` | Hero bottom-left decorative circle (purple radial gradient) |
| `.stu-hero-content` | Hero left content (z-index: 1) |
| `.stu-hero-greeting` | Greeting label (0.85rem, uppercase, color: #0AFFFF) |
| `.stu-hero h1` | Hero heading (font-size: 2.25rem) |
| `.stu-hero-sub` | Hero subtitle (0.95rem, max-width: 500px) |
| `.stu-hero-meta` | Hero right metadata column |
| `.stu-info-card` | Student info card (glassmorphism: backdrop-filter blur, border-radius: 16px) |
| `.info-item` | Individual info row (flex, border-radius: 12px, hover transition) |
| `.info-item:hover` | Info item hover (cyan tint background, translateX(4px)) |
| `.info-icon` | Info icon box (40×40px, cyan background) |
| `.info-label` | Info field label (0.75rem, uppercase) |
| `.info-value` | Info field value (1rem, font-weight: 500) |
| `.info-value.mono` | Monospace info value (Courier New) |
| `.stu-action-card` | Action card (centered flex column, border-radius: 20px, transition) |
| `.stu-action-card::before` | Top accent bar (3px, initially opacity: 0) |
| `.stu-action-card.cyan::before` | Cyan gradient accent bar |
| `.stu-action-card.purple::before` | Purple gradient accent bar |
| `.stu-action-card:hover` | Action card hover (translateY(-8px), shadow) |
| `.stu-action-card.cyan:hover` | Cyan border on hover |
| `.stu-action-card.purple:hover` | Purple border on hover |
| `.stu-action-card:hover::before` | Reveals accent bar on hover |
| `.stu-action-icon` | Action icon circle (70×70px, glow shadow) |
| `.stu-action-card.cyan .stu-action-icon` | Cyan icon variant (rgba(10,255,255,0.1)) |
| `.stu-action-card.purple .stu-action-icon` | Purple icon variant (rgba(122,34,253,0.12)) |
| `.stu-action-card:hover .stu-action-icon` | Icon scales up on hover (scale: 1.1) |
| `.stu-action-title` | Action card title (1.25rem, font-weight: 700) |
| `.stu-action-desc` | Action card description (0.85rem, muted color) |
| `.stu-action-btn` | Action card button wrapper (relative, z-index: 1) |
| `[dir="rtl"] .stu-hero-meta` | RTL: align-items flex-start |
| `@media (max-width: 768px) .stu-hero` | Mobile hero: column direction |
| `@media (max-width: 768px) .stu-hero h1` | Mobile hero heading (1.8rem) |
| `@media (max-width: 768px) .stu-hero-meta` | Mobile meta alignment |

---

## 3. Blade Files WITHOUT `<style>` Blocks

The following files were inspected and contain **no inline CSS** — they rely entirely on `public/css/dashboard.css`, `public/css/auth.css`, or Bootstrap classes:

| File | Layout |
|------|--------|
| `resources/views/welcome.blade.php` | `layouts.auth` |
| `resources/views/layouts/auth.blade.php` | — (is a layout) |
| `resources/views/layouts/app.blade.php` | — (is a layout) |
| `resources/views/components/language-switcher.blade.php` | — (component) |
| `resources/views/errors/403.blade.php` | — |
| `resources/views/errors/404.blade.php` | — |
| `resources/views/errors/500.blade.php` | — |
| `resources/views/vendor/pagination/cms.blade.php` | — |
| `resources/views/admin/dashboard.blade.php` | `layouts.app` |
| `resources/views/admin/doctors.blade.php` | `layouts.app` |
| `resources/views/admin/academic-years/index.blade.php` | `layouts.app` |
| `resources/views/admin/academic-years/create.blade.php` | `layouts.app` |
| `resources/views/admin/academic-years/edit.blade.php` | `layouts.app` |
| `resources/views/admin/doctors/assign.blade.php` | `layouts.app` |
| `resources/views/admin/doctors/assignments.blade.php` | `layouts.app` |
| `resources/views/admin/doctors/index.blade.php` | `layouts.app` |
| `resources/views/admin/doctors/pending.blade.php` | `layouts.app` |
| `resources/views/admin/doctors/rejected.blade.php` | `layouts.app` |
| `resources/views/doctor/ideas/create.blade.php` | `layouts.app` |
| `resources/views/doctor/ideas/edit.blade.php` | `layouts.app` |
| `resources/views/doctor/ideas/index.blade.php` | `layouts.app` |
| `resources/views/doctor/requests/index.blade.php` | `layouts.app` |
| `resources/views/doctor/students/import.blade.php` | `layouts.app` |
| `resources/views/doctor/students/index.blade.php` | `layouts.app` |
| `resources/views/doctor/teams/edit.blade.php` | `layouts.app` |
| `resources/views/doctor/teams/index.blade.php` | `layouts.app` |
| `resources/views/doctor/teams/preview.blade.php` | `layouts.app` |
| `resources/views/doctor/workspaces/phases/create.blade.php` | `layouts.app` |
| `resources/views/doctor/workspaces/phases/edit.blade.php` | `layouts.app` |
| `resources/views/doctor/workspaces/show.blade.php` | `layouts.app` |
| `resources/views/doctor/workspaces/tasks/create.blade.php` | `layouts.app` |
| `resources/views/doctor/workspaces/tasks/edit.blade.php` | `layouts.app` |
| `resources/views/doctor/workspaces/tasks/show.blade.php` | `layouts.app` |
| `resources/views/student/activate.blade.php` | — |
| `resources/views/student/login.blade.php` | — |
| `resources/views/student/team.blade.php` | `layouts.app` |
| `resources/views/student/workspace/show.blade.php` | `layouts.app` |
| `resources/views/student/workspace/subtasks/show.blade.php` | `layouts.app` |
| `resources/views/student/workspace/tasks/show.blade.php` | `layouts.app` |

---

## 4. `public/css/auth.css` — Selectors Inventory (551 lines)

| Selector / Rule | Description |
|-----------------|-------------|
| `:root` | CSS custom properties (student/doctor theme colors, backgrounds, inputs, text) |
| `body` | Base body (min-height: 100vh, radial gradient background, Poppins font) |
| `h1, h2, h3, h4, h5, h6` | Heading weight (600) |
| `a` | Link base (text-decoration: none, transition) |
| `.auth-wrapper` | Two-column flex container |
| `.auth-sidebar` | Left branding column (flex 1, padding, centered content) |
| `.auth-form-container` | Right form column (flex 1, centered) |
| `.brand-tag` | Pill-style branding label |
| `.brand-tag.student` | Student color variant |
| `.brand-tag.doctor` | Doctor color variant |
| `.brand-tag::before` | Colored dot in brand tag |
| `.brand-tag.student::before` | Student dot color |
| `.brand-tag.doctor::before` | Doctor dot color |
| `.auth-heading` | Large hero heading (3.5rem, letter-spacing: -0.03em) |
| `.text-gradient-student` | Student cyan gradient text (webkit-background-clip) |
| `.text-gradient-doctor` | Doctor purple gradient text |
| `.auth-subheading` | Heading subtitle (1.1rem, muted, max-width: 500px) |
| `.auth-card` | Form card (glassmorphism dark, max-width: 460px) |
| `.auth-card.large` | Wider card variant (max-width: 560px) |
| `.auth-card-header` | Card header spacing |
| `.auth-card-title` | Card title (1.75rem) |
| `.auth-card-subtitle` | Card subtitle (0.95rem, muted) |
| `.register-title` | Registration mode pill badge |
| `.register-title.doctor` | Doctor variant colors |
| `.register-title.doctor i` | Doctor icon color |
| `.register-title.student` | Student variant colors |
| `.register-title.student i` | Student icon color |
| `.form-label` | Form label (0.75rem, uppercase, letter-spacing) |
| `.form-control, .form-select` | Dark input fields (background: #0C101A) |
| `.form-control:focus, .form-select:focus` | Focus state (box-shadow: none base) |
| `.student-theme .form-control:focus` | Student focus ring (cyan) |
| `.doctor-theme .form-control:focus` | Doctor focus ring (purple) |
| `.form-control::placeholder` | Placeholder color (#4a5568) |
| `.input-icon-wrapper` | Input with prefix icon wrapper |
| `.input-icon-wrapper i.prefix-icon` | Absolutely positioned prefix icon |
| `.input-icon-wrapper .form-control` | Left padding for icon |
| `.input-icon-wrapper .btn-toggle-password` | Eye toggle button (absolute, right) |
| `.input-icon-wrapper .btn-toggle-password:hover` | Eye toggle hover |
| `.input-icon-wrapper .btn-forgot` | Forgot password link (absolute, top) |
| `.btn-primary-student` | Student primary button (cyan background) |
| `.btn-primary-student:hover` | Student button hover (translateY(-2px), glow) |
| `.btn-primary-doctor` | Doctor primary button (purple background) |
| `.btn-primary-doctor:hover` | Doctor button hover (translateY(-2px), glow) |
| `.auth-tabs` | Tab switcher container (Login / Register) |
| `.auth-tab` | Individual tab (flex: 1, transition) |
| `.auth-tab.active.student-theme` | Active student tab |
| `.auth-tab.active.doctor-theme` | Active doctor tab |
| `.auth-tab:hover:not(.active)` | Hover on inactive tab |
| `.radio-pill-group` | Auth.css radio pill group (flex, gap: 1rem) |
| `.radio-pill` | Radio pill wrapper |
| `.radio-pill input[type="radio"]` | Hidden radio |
| `.radio-pill label` | Pill label styling |
| `.student-theme .radio-pill input[type="radio"]:checked + label` | Student checked pill |
| `.doctor-theme .radio-pill input[type="radio"]:checked + label` | Doctor checked pill |
| `.student-theme .radio-pill input[type="radio"]:checked + label::before` | Student checked dot |
| `.doctor-theme .radio-pill input[type="radio"]:checked + label::before` | Doctor checked dot |
| `.auth-footer-text` | Footer link text (centered, 0.9rem) |
| `.auth-footer-text a` | Footer link (font-weight: 600) |
| `.student-theme .auth-footer-text a` | Student footer link color |
| `.doctor-theme .auth-footer-text a` | Doctor footer link color |
| `.choose-container` | Role selection page container (flex, centered) |
| `.choose-cards` | Role cards wrapper (flex, gap: 2rem, wrap) |
| `.role-card` | Role selection card (dark glass, 350px width) |
| `.role-card:hover` | Role card hover (translateY(-5px)) |
| `.role-icon` | Role card icon box (60×60px) |
| `.role-card.student:hover .role-icon` | Student icon glow on hover |
| `.role-card.doctor:hover .role-icon` | Doctor icon glow on hover |
| `.invalid-feedback` | Bootstrap validation message (color: #f87171) |
| `.is-invalid ~ .invalid-feedback` | Shows feedback on invalid |
| `.is-invalid` | Invalid field border color |
| `.auth-nav` | Auth page navigation bar |
| `.auth-brand` | Brand logo link (1.5rem, cyan color) |
| `.auth-nav-links` | Nav links container (flex, gap: 2.5rem) |
| `.auth-nav-links a` | Nav link styling |
| `.auth-nav-links a:hover` | Nav link hover |
| `.auth-nav-links a.active-pill` | Active nav link pill style |
| `.doctor-theme .auth-nav-links a.active-pill` | Doctor active nav pill |
| `.auth-nav-profile` | Right nav profile area |
| `.icon-btn` | Icon-only button (no border, no bg) |
| `.icon-btn:hover` | Icon button hover |
| `.profile-pill` | Avatar circle (36×36px) |
| `.profile-pill img` | Avatar image (cover fit) |
| `.bottom-footer` | Auth page footer (flex, space-between) |
| `.bottom-footer .footer-brand` | Footer brand name |
| `.bottom-footer .footer-brand span` | Footer brand light-weight span |
| `.bottom-footer .footer-links a` | Footer links |
| `.bottom-footer .footer-links a:hover` | Footer link hover |
| `.bottom-footer .footer-copy` | Footer copyright |
| `.form-panels-wrapper` | Panel transition wrapper (overflow: hidden, height transitions) |
| `.form-panel` | Base panel (width: 100%) |
| `.form-panel.panel-visible` | Visible panel (opacity: 1, position: relative) |
| `.form-panel.panel-hidden` | Hidden panel (position: absolute, opacity: 0, visibility: hidden) |
| `.form-panel.panel-exiting` | Exiting panel (opacity: 0, translateY(-10px)) |
| `.form-panel.panel-entering` | Entering panel (opacity: 0, visibility: visible) |
| `@media (max-width: 991px) .auth-wrapper` | Mobile: column direction |
| `@media (max-width: 991px) .auth-sidebar` | Mobile: centered, reduced padding |
| `@media (max-width: 991px) .auth-sidebar .brand-tag` | Mobile: centered brand tag |
| `@media (max-width: 991px) .auth-nav` | Mobile: reduced nav padding |
| `@media (max-width: 991px) .auth-nav-links` | Mobile: hide nav links |
| `@media (max-width: 991px) .bottom-footer` | Mobile: column footer |
| `@media (max-width: 575px) .auth-sidebar` | Small mobile: hide sidebar |
| `@media (max-width: 575px) .auth-card, .auth-card.large` | Small mobile: full-width card |
| `@media (max-width: 575px) .auth-form-container` | Small mobile: reduced padding |

---

## 5. `public/css/dashboard.css` — Selectors Inventory (1,419 lines)

| Selector / Rule | Description |
|-----------------|-------------|
| `:root` | CSS custom properties (brand accents, backgrounds, surfaces, text, sidebar width, layout heights, font, easing, backward-compatible aliases) |
| `*, *::before, *::after` | Box-sizing reset |
| `html` | Height: 100% |
| `body` | Base body (min-height: 100vh, dark gradient background, Poppins font) |
| `h1…h6` | Heading weight (600), margin-bottom: 0 |
| `a` | Link base (text-decoration: none, transition) |
| `p` | Paragraph color (var(--text-muted)) |
| `.cms-nav` | Fixed top navigation bar (height: 64px, z-index: 1000, backdrop-filter) |
| `.cms-brand` | Brand logo (1.3rem, purple, flex, letter-spacing) |
| `.cms-brand-dot` | Animated pulsing brand dot |
| `@keyframes pulse-dot` | Brand dot pulse animation (opacity + scale) |
| `.cms-nav-toggle` | Hamburger button (hidden on desktop) |
| `.cms-nav-toggle:hover` | Hamburger hover state |
| `.cms-nav-right` | Right nav area (flex, gap: 1.25rem) |
| `.cms-user-pill` | User info pill (border-radius: 50px, purple tint) |
| `.cms-user-pill .user-avatar` | Avatar circle (28×28px, gradient background) |
| `.cms-user-pill .user-name` | Username text (ellipsis overflow) |
| `.cms-user-pill .user-role` | User role text (faint color) |
| `.cms-logout-btn` | Logout button in nav (ghost button style) |
| `.cms-logout-btn:hover` | Logout hover (red tint) |
| `.cms-lang-switch` | Language switcher container |
| `.cms-lang-switch form` | Lang switch form (inline) |
| `.cms-lang-switch button` | Lang button (border, faint color) |
| `.cms-lang-switch button.active` | Active language button (purple tint) |
| `.cms-lang-switch button:hover:not(.active)` | Inactive lang button hover |
| `.cms-sidebar` | Fixed sidebar (width: 260px, z-index: 900, dark background) |
| `.cms-sidebar-nav` | Sidebar nav list (flex column, scrollable) |
| `.cms-sidebar-nav::-webkit-scrollbar` | Thin sidebar scrollbar (4px) |
| `.cms-sidebar-nav::-webkit-scrollbar-track` | Transparent scrollbar track |
| `.cms-sidebar-nav::-webkit-scrollbar-thumb` | Scrollbar thumb |
| `.cms-nav-section` | Section label in sidebar (uppercase, faint, 0.65rem) |
| `.cms-nav-item` | Sidebar nav link (flex, border-radius: 10px, transition) |
| `.cms-nav-item i` | Nav item icon (20px wide, centered) |
| `.cms-nav-item:hover` | Nav item hover (purple tint background) |
| `.cms-nav-item.active` | Active nav item (purple background, light text) |
| `.cms-nav-item.active i` | Active nav icon (accent-admin color) |
| `.cms-sidebar-logout` | Sidebar logout area (border-top, padding) |
| `.cms-sidebar-logout form` | Logout form (full width) |
| `.cms-sidebar-logout button` | Logout button (full width, flex) |
| `.cms-sidebar-logout button i` | Logout icon |
| `.cms-sidebar-logout button:hover` | Logout hover (red tint) |
| `.cms-main` | Main content area (margin-left: 260px, margin-top: 64px, padding) |
| `.cms-footer` | Fixed footer (bottom, height: 52px, z-index: 1000, backdrop-filter) |
| `.cms-footer-brand` | Footer brand text |
| `.cms-footer-links` | Footer links container (flex, gap: 1.5rem) |
| `.cms-footer-links a` | Footer link styling |
| `.cms-footer-links a:hover` | Footer link hover |
| `.cms-page-header` | Page header section (flex, space-between, margin-bottom: 2rem) |
| `.cms-page-header h1` | Page heading (1.6rem, weight: 700) |
| `.cms-page-header p` | Page subtitle (0.88rem, muted) |
| `.cms-breadcrumb` | Breadcrumb nav (flex, gap: 0.4rem, 0.78rem) |
| `.cms-breadcrumb a` | Breadcrumb link (color: #a78bfa) |
| `.cms-breadcrumb span` | Current breadcrumb (faint color) |
| `.cms-card` | Glass card container (border-radius: 16px, surface color, border, transition) |
| `.cms-card:hover` | Card hover state |
| `.cms-card-header` | Card header (padding: 1.25rem 1.5rem, border-bottom, flex) |
| `.cms-card-header h2, h3, h4` | Card header headings (1rem, weight: 600) |
| `.cms-card-body` | Card body (padding: 1.5rem / overridden to 1.75rem) |
| `.cms-stat-card` | KPI stat card (flex, border-radius: 16px, transition) |
| `.cms-stat-card:hover` | Stat card hover (translateY(-3px), shadow) |
| `.cms-stat-icon` | Stat icon box (52×52px, border-radius: 12px) |
| `.cms-stat-icon.purple` | Purple icon variant |
| `.cms-stat-icon.cyan` | Cyan icon variant |
| `.cms-stat-icon.green` | Green icon variant |
| `.cms-stat-icon.amber` | Amber icon variant |
| `.cms-stat-icon.red` | Red icon variant |
| `.cms-stat-icon.blue` | Blue icon variant |
| `.cms-stat-value` | Stat number (1.8rem, weight: 700) |
| `.cms-stat-label` | Stat description (0.8rem, muted) |
| `.cms-table-wrapper` | Table horizontal scroll container |
| `.cms-table` | Base table (border-collapse, 0.88rem) |
| `.cms-table thead th` | Table header (uppercase, faint, dark background) |
| `.cms-table tbody td` | Table cell (padding, muted color) |
| `.cms-table tbody tr:last-child td` | Last row (no border) |
| `.cms-table tbody tr:hover td` | Row hover (purple tint) |
| `.cms-table .td-name` | Name column (bold, primary color) |
| `.cms-table .td-mono` | Mono column (Courier New, 0.8rem, faint) |
| `.cms-badge` | Base badge (inline-flex, border-radius: 50px, 0.72rem, uppercase) |
| `.cms-badge-success` | Green badge |
| `.cms-badge-warning` | Amber badge |
| `.cms-badge-danger` | Red badge |
| `.cms-badge-purple` | Purple badge |
| `.cms-badge-muted` | Grey badge |
| `.cms-badge-cyan` | Cyan badge |
| `.cms-btn` | Base button (inline-flex, border-radius: 8px, 0.83rem, transition) |
| `.cms-btn i` | Button icon (0.9rem) |
| `.cms-btn-primary` | Primary button (purple background) |
| `.cms-btn-primary:hover` | Primary hover (darker purple, glow, translateY(-1px)) |
| `.cms-btn-success` | Success button (green tint) |
| `.cms-btn-success:hover` | Success hover |
| `.cms-btn-danger` | Danger button (red tint) |
| `.cms-btn-danger:hover` | Danger hover |
| `.cms-btn-warning` | Warning button (amber tint) |
| `.cms-btn-warning:hover` | Warning hover |
| `.cms-btn-ghost` | Ghost button (transparent, border) |
| `.cms-btn-ghost:hover` | Ghost hover |
| `.cms-btn-sm` | Small button modifier |
| `.cms-btn-lg` | Large button modifier |
| `.cms-btn:disabled` | Disabled button (opacity: 0.4) |
| `.cms-btn-secondary` | Secondary button (very light bg, muted color) |
| `.cms-btn-secondary:hover` | Secondary hover (purple tint) |
| `.cms-form-group` | Form group wrapper (margin-bottom: 1.25rem) |
| `.cms-form-label` | Form label (0.72rem, uppercase, letter-spacing) |
| `.cms-form-control` | Dark input (background: #0C101A, border: #1E2536) |
| `.cms-form-control:focus` | Input focus (purple border + glow) |
| `.cms-form-control::placeholder` | Placeholder color (#4a5568) |
| `.cms-form-control.is-invalid` | Invalid input (red border) |
| `.cms-invalid-feedback` | Validation error text (hidden by default) |
| `.cms-form-control.is-invalid~.cms-invalid-feedback` | Shows feedback on invalid |
| `.cms-checkbox-item` | Checkbox list item (flex, border, hover transition) |
| `.cms-checkbox-item:hover` | Checkbox hover (purple tint) |
| `.cms-checkbox-item input[type="checkbox"]` | Checkbox input (accent-color: purple) |
| `.cms-checkbox-label` | Checkbox label text |
| `.cms-checkbox-item:has(input:checked)` | Checked checkbox item (purple tint) |
| `.cms-checkbox-item:has(input:checked) .cms-checkbox-label` | Checked label color |
| `.cms-alert` | Base alert (flex, border-radius: 10px, animated slide-down) |
| `@keyframes slide-down` | Alert entrance animation |
| `.cms-alert-success` | Green alert |
| `.cms-alert-danger` | Red alert |
| `.cms-alert-warning` | Amber alert |
| `.cms-alert-info` | Blue alert |
| `.cms-alert i` | Alert icon |
| `.cms-alert-content` | Alert body (flex: 1) |
| `.cms-alert-dismiss` | Alert close button |
| `.cms-alert-dismiss:hover` | Close button hover |
| `.cms-empty-state` | Empty state (centered, padding: 3rem) |
| `.cms-empty-state i` | Empty state icon (2.5rem, faint) |
| `.cms-empty-state p` | Empty state description |
| `.cms-action-card` | Quick action card (flex, border-radius: 12px) |
| `.cms-action-card:hover` | Action card hover (purple tint, translateY(-2px)) |
| `.cms-action-card .action-icon` | Action card icon (42×42px) |
| `.cms-action-card .action-label` | Action label (0.88rem, bold) |
| `.cms-action-card .action-sub` | Action subtitle (0.75rem, faint) |
| `.cms-action-card .action-arrow` | Arrow icon (auto left-margin) |
| `.cms-action-card:hover .action-arrow` | Arrow shifts right on hover |
| `.cms-level-pill` | Level badge pill (inline-flex, border-radius: 50px, purple tint) |
| `.cms-level-pill:hover` | Level pill hover (darker purple) |
| `[dir="rtl"] .cms-sidebar` | RTL sidebar (right side, left border) |
| `[dir="rtl"] .cms-main` | RTL main content margin |
| `[dir="rtl"] .cms-table thead th` | RTL table header (text-align: end) |
| `[dir="rtl"] .cms-action-card .action-arrow` | RTL arrow (margin-right: auto) |
| `[dir="rtl"] .cms-action-card:hover .action-arrow` | RTL arrow translateX(-3px) |
| `@media (max-width: 991px) .cms-sidebar` | Mobile: sidebar hidden (translateX(-100%)) |
| `@media (max-width: 991px) [dir="rtl"] .cms-sidebar` | RTL mobile: translateX(100%) |
| `@media (max-width: 991px) .cms-sidebar.open` | Mobile sidebar open state |
| `@media (max-width: 991px) .cms-main` | Mobile: no sidebar margin |
| `@media (max-width: 991px) .cms-nav-toggle` | Mobile: show hamburger |
| `@media (max-width: 991px) .cms-footer-links` | Mobile: hide footer links |
| `@media (max-width: 991px) .cms-nav` | Mobile: reduced padding |
| `@media (max-width: 575px) .cms-main` | Small mobile: reduced padding |
| `@media (max-width: 575px) .cms-page-header` | Small mobile: column direction |
| `.cms-overlay` | Mobile sidebar backdrop (hidden, fixed, blur) |
| `.cms-overlay.show` | Backdrop visible state |
| `.cms-pagination` | Pagination container (flex, centered) |
| `.cms-page-btn` | Pagination button (36×36px, border-radius: 8px) |
| `.cms-page-btn:not(.active):not(.disabled):not(.cms-page-dots):hover` | Page button hover (purple tint) |
| `.cms-page-btn.active` | Active page button (purple, shadow glow) |
| `.cms-page-btn.disabled` | Disabled page button (opacity: 0.3) |
| `.cms-page-btn.cms-page-dots` | Ellipsis separator |
| `.cms-page-btn i` | Pagination icon |
| `[dir="rtl"] .cms-page-btn i.bi-chevron-left` | RTL chevron flip |
| `[dir="rtl"] .cms-page-btn i.bi-chevron-right` | RTL chevron flip |
| `@media (max-width: 480px) .cms-page-btn` | Small mobile: smaller pagination |
| `.cms-main > *` | Page entrance animation (fadeInUp, 0.45s) |
| `.cms-main > *:nth-child(1..5)` | Staggered animation delays (0.05s–0.25s) |
| `@keyframes fadeInUp` | Page entrance keyframes (opacity 0→1, translateY 16px→0) |
| `.cms-nav-item.active` | Active item (position: relative for pseudo-element) |
| `.cms-nav-item.active::before` | Active left indicator bar (3px, purple glow) |
| `[dir="rtl"] .cms-nav-item.active::before` | RTL active bar (right side) |
| `.cms-brand:hover` | Brand hover (lighter purple) |
| `.cms-brand:hover .cms-brand-dot` | Brand dot glow on hover |
| `.cms-card-body` (second declaration) | Card body padding override (1.75rem) |
| `.cms-table tbody tr` | Table row transition |
| `.cms-table tbody tr:hover td` | Table row hover override (primary text) |
| `::-webkit-scrollbar` | Global scrollbar (6×6px) |
| `::-webkit-scrollbar-track` | Global scrollbar track |
| `::-webkit-scrollbar-thumb` | Global scrollbar thumb (purple) |
| `::-webkit-scrollbar-thumb:hover` | Darker scrollbar on hover |
| `.cms-alert` (second declaration) | Alert position: relative override |
| `a:focus-visible, button:focus-visible` | Accessibility focus ring (purple outline) |
| `.cms-sidebar` (second declaration) | Sidebar box-shadow addition |
| `.cms-user-pill:hover .user-avatar` | Avatar glow on user pill hover |
| `.cms-card` (second declaration) | Card transition (border-color + box-shadow) |
| `.cms-card:hover` (second declaration) | Card hover shadow and border override |

---

## 6. Summary

| Category | Count |
|----------|-------|
| Files in `public/css/` | **2** |
| Blade files with `<style>` blocks | **7** |
| Blade files without `<style>` blocks | **39** |
| Total selectors in `auth.css` | ~90 |
| Total selectors in `dashboard.css` | ~130 |
| Duplicate style blocks found | **1 pair** (`auth/login.blade.php` ↔ `auth/register.blade.php`) |

### Key Observations

1. **Duplicate CSS:** `auth/login.blade.php` and `auth/register.blade.php` contain identical `.radio-pill*` style blocks (57 lines each). These should be extracted into `auth.css`.

2. **Large inline blocks:** `doctor/dashboard.blade.php` (475 lines) and `student/dashboard.blade.php` (260 lines) contain significant inline CSS that could be extracted into `dashboard.css` for better maintainability.

3. **Design system consistency:** Both `doctor/workspaces/index.blade.php` (98 lines) and `doctor/teams/distribute.blade.php` (80 lines) define selectors that partially overlap with `dashboard.css` (e.g., `.doc-stat-card:hover`).

4. **No framework CSS files:** The project does not use a compiled Vite/Mix asset — all styles are in `public/css/` directly or inline. The `hot` file is present, suggesting dev-server mode may be active.

5. **RTL support:** Both global CSS files and several inline blocks include `[dir="rtl"]` overrides for Arabic language support.
