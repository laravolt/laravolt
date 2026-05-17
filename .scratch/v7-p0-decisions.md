# Laravolt v7 — P0 Decisions Log

This file records the design decisions made while closing the P0 list
in `v7-stable-issues-from-voltprocure.md`. Each section is short on
purpose; the source of truth is the code.

## P0-1: Unify TableView / Suitable / `<x-volt-table>` Preline styling

### Blessed pattern

Three layers, with clear responsibility:

1. **`<x-volt-table>`** (anonymous Blade component at
   `resources/views/components/table.blade.php`) is the **low-level
   styling primitive**. It is slot-based: caller provides `<thead>`
   and `<tbody>`. It is responsible for:
   - Preline-styled `<table>` (divide, dark mode, hover)
   - Horizontal overflow wrapper (responsive)
   - Optional bordered / striped / hover modifiers via attributes
   It does **not** know about data, columns, pagination, sort, search,
   or filters.

2. **Suitable** (`packages/suitable/resources/views/table.blade.php`
   and `container.blade.php`) renders **through `<x-volt-table>`**
   for the static / server-rendered case. Container provides the
   bordered Preline card; table.blade.php provides thead/tbody slots.

3. **`Laravolt\Ui\TableView`** (Livewire) renders **through
   `<x-volt-table>`** for the Livewire case via
   `resources/views/ui-component/table-view/table.blade.php`.
   Container (`table-view/container.blade.php`) provides the Preline
   card, search, filter, pagination, and per-page controls.

### What changed

- `resources/views/components/table.blade.php` rewritten as a thin,
  slot-only primitive. The legacy data-driven `:headers` / `:rows`
  API is removed. No app in the three blessed repos used it; Suitable
  always passed slots; VoltProcure used raw `<table>`. Removing
  unblocks consistent v7 styling.
- `resources/views/ui-component/table-view/table.blade.php` now wraps
  its `<thead>` / `<tbody>` in `<x-volt-table>` so Livewire TableView
  shares the same primitive as Suitable.
- `packages/suitable/resources/views/empty.blade.php` and
  `resources/views/ui-component/table-view/empty.blade.php` aligned
  to the same icon + heading + helper-text empty state.

### Acceptance

- Generated Thunderclap CRUD index (`index.blade.php.stub` →
  `@livewire(... TableView::class)`) renders through the unified
  primitive without any app-local table partials.
- Suitable static rendering and TableView Livewire rendering share
  identical table chrome (divider, hover, header background, header
  text style).
- Feature test
  `tests/Unit/Thunderclap/GeneratedIndexStubTest.php` asserts the
  generated index Blade structure (single `<x-volt-app>`,
  `<x-volt-link-button>` for the add button, and the `@livewire`
  TableView reference).

### Backwards compatibility note

The data-driven `<x-volt-table :headers :rows />` API is removed in
v7. No public docs documented it. If a downstream app uses it,
migration is: pass `<thead>` / `<tbody>` slots directly (the standard
Laravel/Preline pattern) or use `Laravolt\Ui\TableView` /
`Laravolt\Suitable\Builder` for data-driven tables.

---

## P0-2: Thunderclap end-to-end validation

See `.scratch/v7-p0-thunderclap-smoke/REPORT.md` (created during P0-2).

---

## P0-3: Permission cache hardening

`HasRoleAndPermission::permissions()` now:

- caches a **primitive array of `[id => name]` pairs** instead of an
  Eloquent Collection, so cache stores that serialize state (database,
  file, redis with PHP classes that move) cannot return
  `__PHP_Incomplete_Class` for the typed return value.
- validates the cached payload shape before returning; if it does not
  look like the expected primitive array, the key is forgotten and
  rebuilt from the database.
- still uses the same cache key
  `users.{id}.permissions`, so `AccessControlInvalidator::flushUser()`
  behavior is preserved.

Regression test:
`tests/Feature/Acl/PermissionCacheHardeningTest.php` seeds a malformed
cache payload (mimicking `__PHP_Incomplete_Class`) and asserts that
`hasPermission()` recovers gracefully without throwing TypeError.

---

## P0-4: Starter-kit Docker templates

Templates land in `laravel-starter-kit`, not in the platform package.
Rationale: Docker files are app-shape, not library-shape; users see
them first in the starter kit when they run
`composer create-project laravolt/laravel-starter-kit`.

Files:

- `docker/php/dev.Dockerfile` — PHP 8.4 CLI + Composer + Node, SQLite
  ready, runs `php artisan serve` for fast feedback. Mirrors
  VoltProcure dev image.
- `docker/frankenphp/prod.Dockerfile` — FrankenPHP + PHP 8.4
  Octane-ready, bakes vendor + Vite assets. Mirrors VoltProcure prod
  image.
- `docker-compose.dev.yml` — SQLite quickstart, code mounted as a
  volume, Vite dev server optional.
- `docker-compose.prod.yml` — FrankenPHP + Postgres option (Postgres
  service commented for SQLite default; flip to enable).
- `DOCKER.md` — image targets, env vars, FrankenPHP / Octane notes,
  SQLite vs Postgres, where to look when things break.

We do **not** build & run the prod image in this Phase 2 task; that is
deferred to a release-side smoke test.
