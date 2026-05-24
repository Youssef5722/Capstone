# Patch B Audit Report

**Date:** 2026-05-24
**Scope:** Read-only audit of Blade view files (`resources/views/`)
**Objective:** Identify untranslated strings, RTL configuration issues, and non-RTL-aware direction classes.

---

## 1. Untranslated Strings
The following hardcoded English strings were found outside of the `__()` localization helper. Many of these are placeholders or footer links that need to be extracted into localization files.

| File Path | Hardcoded String | Line | Note |
|---|---|---|---|
| `admin/academic-years/create.blade.php` | `e.g. 2024/2025` | 34 | input placeholder |
| `auth/login.blade.php` | `Dr. Jane Doe` | 184 | input placeholder |
| `auth/login.blade.php` | `jane.doe.edu` | 198 | input placeholder |
| `auth/login.blade.php` | `e.g. 29901011234567` | 218 | input placeholder |
| `auth/register.blade.php` | `Dr. Jane Doe` | 181 | input placeholder |
| `auth/register.blade.php` | `jane.doe.edu` | 195 | input placeholder |
| `auth/register.blade.php` | `e.g. 29901011234567` | 215 | input placeholder |
| `doctor/ideas/create.blade.php` | `e.g. Smart Campus Navigation System` | 50 | input placeholder |
| `doctor/ideas/create.blade.php` | `Describe the project idea, objectives...` | 66 | textarea placeholder |
| `doctor/requests/index.blade.php` | `pending` | 29 | status text node |
| `doctor/students/import.blade.php` | `Expected Excel columns:` | 55 | text node |
| `doctor/students/import.blade.php` | `name` | 56 | text node |
| `doctor/students/import.blade.php` | `university_id` | 57 | text node |
| `doctor/students/import.blade.php` | `(required)` | 57 | text node |
| `layouts/app.blade.php` | `Privacy Policy` | 239 | footer link |
| `layouts/app.blade.php` | `Terms of Service` | 240 | footer link |
| `layouts/app.blade.php` | `University Guidelines` | 241 | footer link |
| `layouts/auth.blade.php` | `Home` | 30 | nav link |
| `layouts/auth.blade.php` | `Dashboard` | 37, 40 | nav link |
| `layouts/auth.blade.php` | `Sign out` | 71 | title attribute |
| `layouts/auth.blade.php` | `Privacy Policy` | 88 | footer link |
| `layouts/auth.blade.php` | `Terms of Service` | 89 | footer link |
| `layouts/auth.blade.php` | `University Guidelines` | 90 | footer link |
| `student/activate.blade.php` | `e.g. STU-2024-XXXX.edu` | 59 | input placeholder |
| `student/activate.blade.php` | `your.com` | 128 | input placeholder |
| `student/login.blade.php` | `e.g. STU-2024-XXXX.edu` | 59 | input placeholder |
| `student/login.blade.php` | `name.edu` | 112 | input placeholder |
| `student/team.blade.php` | `Leader` | 91 | title attribute |

*(Note: "CMS" brand mentions were excluded from this list as brand names are typically not translated).*

---

## 2. RTL Issues (Layouts)
A thorough inspection of `resources/views/layouts/app.blade.php` and `resources/views/layouts/auth.blade.php` yields the following results:

- **Is `dir="rtl"` applied conditionally based on locale?**
  **✅ Yes.** Both layouts correctly implement `<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">`.

- **Is `bootstrap.rtl.min.css` loaded when locale = ar?**
  **✅ Yes.** The RTL stylesheet is loaded inside an `@if(app()->getLocale() === 'ar')` block.

- **Is `bootstrap.min.css` (LTR) loaded when locale = en?**
  **✅ Yes.** The standard LTR stylesheet is correctly loaded inside the `@else` block.

**Conclusion:** The root RTL configuration in the layout shells is fully functioning and correctly implemented.

---

## 3. Flex/Grid Direction Issues
Bootstrap 5 relies on logical properties (start/end) instead of physical directions (left/right) to support seamless LTR/RTL switching. An audit of all Blade views was conducted looking for legacy directional classes:
- `ml-*` / `mr-*` (Margin left/right)
- `pl-*` / `pr-*` (Padding left/right)
- `text-left` / `text-right`
- `float-left` / `float-right`

**Findings:**
**✅ Zero Instances Found.** 
The entire codebase exclusively uses Bootstrap 5's RTL-aware logical classes (`ms-*`, `me-*`, `ps-*`, `pe-*`, `text-start`, `text-end`). Because `dir="rtl"` is applied properly in the layout, these logical classes will flip margins, padding, and text alignment automatically without any layout breakage. No manual directional CSS overrides are required.
