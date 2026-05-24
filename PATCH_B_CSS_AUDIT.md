# Patch B CSS Audit Report

**Date:** 2026-05-24
**Scope:** `<style>` blocks in `resources/views/**/*.blade.php`

| File | Selector | Problematic Property | Suggested RTL-safe Replacement |
|---|---|---|---|
| `doctor/dashboard.blade.php:32` | `.doc-hero::before` | `right:` | `inset-inline-end:` |
| `doctor/dashboard.blade.php:44` | `.doc-hero::after` | `left:` | `inset-inline-start:` |
| `doctor/dashboard.blade.php:125` | `.doc-stat-card::before` | `left:` | `inset-inline-start:` |
| `doctor/dashboard.blade.php:126` | `.doc-stat-card::before` | `right:` | `inset-inline-end:` |
| `doctor/dashboard.blade.php:236` | `.doc-level-card-top::after` | `right:` | `inset-inline-end:` |
| `doctor/workspaces/index.blade.php:24` | `.ws-card::before` | `left:` | `inset-inline-start:` |
| `doctor/workspaces/index.blade.php:24` | `.ws-card::before` | `right:` | `inset-inline-end:` |
| `student/dashboard.blade.php:33` | `.stu-hero::before` | `right:` | `inset-inline-end:` |
| `student/dashboard.blade.php:45` | `.stu-hero::after` | `left:` | `inset-inline-start:` |
| `student/dashboard.blade.php:178` | `.stu-action-card::before` | `left:` | `inset-inline-start:` |
| `student/dashboard.blade.php:179` | `.stu-action-card::before` | `right:` | `inset-inline-end:` |
