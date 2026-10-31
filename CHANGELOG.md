# Changelog

## v8.0.0

### Breaking

- **Requires PHP 8.3+ and Laravel 12.68+ or 13.29+.** `illuminate/*` is now `^12.68|^13.29`; Laravel 11 is no longer supported, and the floor excludes framework versions affected by **CVE-2026-114509** / GHSA-f9x3-7vq9-cph6 (critical) (#465, #466).
- **Requires `laravolt/avatar` ^8.0** (same API, Laravel 12.68+/13.29+ only).
- **No longer installs `laravel/octane`, `barryvdh/laravel-dompdf`, `barryvdh/laravel-debugbar` or `spatie/laravel-web-tinker`.** Laravolt itself never used them; they reached applications only transitively. They are listed under `suggest`.
- `larastan/larastan` and `spaze/phpstan-disallowed-calls` moved to `require-dev` (#468).

### Upgrade from v7

1. Make sure the app runs on PHP 8.3+ and Laravel 12.68+ or 13.29+.
2. If the app uses any of these, require them directly before upgrading:
   ```bash
   composer require laravel/octane            # if you serve with Octane
   composer require barryvdh/laravel-dompdf   # if you use Pdf::
   composer require --dev barryvdh/laravel-debugbar
   composer require spatie/laravel-web-tinker
   composer require --dev larastan/larastan spaze/phpstan-disallowed-calls  # if you run PHPStan
   ```
3. `composer require laravolt/laravolt:^8.0`

### Fixed

- `Acl::syncPermission()` loads existing permissions in one query instead of one per permission (#462).
- Flaky `SyncPermissionCommandTest` assertions (#469).

### Maintenance

- Package test job on PHP 8.3/8.4/8.5 × lowest/stable (#465), Dependabot config (#467), MIT `LICENSE` file (#464).

---

## v7.0.0 — Stable

_Released 2026-07-21._

Laravolt v7 graduates to stable. Highlights since `v7.0.0-beta.4`:

### Generators (Thunderclap)

- Generated CRUD modules ship with **FormRequest-backed PrelineForm** by default: `Requests/Store.php` and `Requests/Update.php` both extend `Illuminate\Foundation\Http\FormRequest`, the controller imports and typehints them, and `_form.blade.php` uses PrelineForm helpers (`form()->action`, `form()->submit`, `form()->linkButton`). Regression test: `tests/Unit/Thunderclap/FormRequestStubsTest.php`.
- Generated routes and menus now share **one permission gate**. By default, every generated module gates on `modules.<module-name>.view` via `can:<permission>` middleware in `routes/web.php` and the same value on the sidebar menu entry. Override per module in `config/{module-name}.php` (string for module-wide gate, `false` for open access, array for OR-checks). Regression test: `tests/Unit/Thunderclap/MenuAclStubsTest.php`.
- Generated test stub seeds an admin role with wildcard `*` permission in `beforeEach()` so generated CRUD tests pass under the new gate.

### UI

- Suitable `Label` column now renders a **Preline pill badge** (`<span class="... rounded-full ...">`) instead of Fomantic `<div class="ui label">`. Backwards-compatible: `addClass()`, `addClassIf()`, `map()` still work; pass Tailwind utilities for custom palettes.
- `Epicentrum\Livewire\UserTable` and `Epicentrum\Table\UserTable`: removed legacy `->addClass('mini')` (Fomantic-only size class, no-op in Tailwind).
- Audit confirms zero Fomantic class leakage in `resources/views/components/` and `resources/views/ui-component/`. New regression test `tests/Unit/Ui/NoFomanticClassesTest.php` scans for forbidden tokens.

### Highlights from earlier beta cycle (carried into stable)

- Unified `TableView` / Suitable / `<x-volt-table>` Preline styling.
- Thunderclap end-to-end validation against fresh starter-kit app.
- Permission cache hardening against stale serialized values (`__PHP_Incomplete_Class` defense).
- Docker dev and prod templates in `laravel-starter-kit`.

### Known out-of-scope items (pre-existing, not v7 regressions)

- `tests/Feature/Acl/HasRoleAndPermissionTest.php` and `tests/Feature/Acl/Models/RoleTest.php` assert integer IDs against the ULID schema. Scheduled for a Phase 4 ULID test sweep.
- `tests/Unit/Media/*` chunk-binding tests need service container bindings fixed.

---

# Changelog Laravolt Versi 5
## Middleware
- Namespace middleware berubah dari `Laravolt\Platform\Http\Middleware` menjadi `Laravolt\Middleware`.
- Middleware perlu ditambahkan secara eksplisit ke app\Http\Kernel.php:
    - `Laravolt\Middleware\DetectFlashMessage`
    - `Laravolt\Middleware\CheckPassword`

## Migrations
- migrations script harus dipublish dulu dengan `php artisan vendor:publish --tag=laravolt-migrations` atau `php artisan laravolt:install`

## Installation
- Tidak perlu compile assets dulu
- Ganti `php artisan ui laravolt` menjadi `php artisan laravolt:install`

## Facade
- `SemanticForm` dihapus, pakai `Form`

## Layout
- ubah dari @extends menjadi component based

## New Feature
- font awesome 5 pro icon: regular, solid, light, duotone

## Configurations
- Config key `route` diubah menjadi `routes` supaya align dengan folder `routes` Laravel

- route('dashboard') dihilangkan, pindah ke route('home')
- registrasi menu di config `config/laravolt/menu/`, tidak boleh ada pemanggilan fungsi helper, semuanya harus static text
- flatten menu array, ubah `permission` jadi `permissions`
