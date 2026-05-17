# Laravolt v7 — P0 Decisions Log

This file records the design decisions made while closing the P0 list
in `v7-stable-issues-from-voltprocure.md`. Each section is short on
purpose; the source of truth is the code.

> **Status (2026-05-17):** P0-1, P0-2, P0-3, P0-4 all shipped. Ready
> for `v7.0.0-beta.1`.

## Shipped commits

Laravolt core (`/Users/rama/laravolt/laravolt`, branch `master`):

- `cc625619` — `feat(ui): unify TableView/Suitable/x-volt-table Preline styling` (P0-1)
- `5f67ba1a` — `chore: gitignore local phpunit.xml override` (P0-0 cleanup)
- `f11d0a83` — `fix(acl): harden permission cache against stale serialized values` (P0-3)
- `86bf541b` — `docs(thunderclap): add v7 end-to-end smoke test report` (P0-2)

Starter kit (`/Users/rama/laravolt/laravel-starter-kit`, branch `main`):

- `19876b3` — `feat(docker): add dev and prod templates for Laravolt v7` (P0-4)

---

## P0-1: Unify TableView / Suitable / `<x-volt-table>` Preline styling

**Status:** shipped (commit `cc625619`).

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

### Backwards compatibility note

The data-driven `<x-volt-table :headers :rows />` API is removed in
v7. No public docs documented it. If a downstream app uses it,
migration is: pass `<thead>` / `<tbody>` slots directly (the standard
Laravel/Preline pattern) or use `Laravolt\Ui\TableView` /
`Laravolt\Suitable\Builder` for data-driven tables.

---

## P0-0: Local `phpunit.xml` cleanup

**Status:** shipped (commit `5f67ba1a`).

The previous run left an untracked `phpunit.xml` in the root of the
laravolt core repo. Diffing against `phpunit.xml.example` showed it
was a local SQLite override (the canonical `phpunit.xml.example` keeps
pgsql active and SQLite commented). We chose to gitignore the local
override rather than commit it, because:

- `phpunit.xml.example` is the published canonical config.
- Different contributors prefer different local DB connections.
- The packaged tooling (`composer test`, CI) always uses
  `phpunit.xml.example` after a `cp`.

Added `/phpunit.xml` to `.gitignore`. No other changes.

---

## P0-3: Permission cache hardening

**Status:** shipped (commit `f11d0a83`).

`HasRoleAndPermission::permissions()` now:

- caches a **primitive array of `['id' => $id, 'name' => $name]`
  shapes** instead of an Eloquent Collection. The primitive shape
  survives database / file / redis cache drivers, class moves, and
  namespace renames — none of which can produce
  `__PHP_Incomplete_Class` for primitive arrays.
- validates the cached payload shape on read via
  `isValidPermissionCache()`. Accepted: an array of arrays, each
  with an `id` key and a string `name` key. Anything else (string,
  int, `__PHP_Incomplete_Class`, malformed array) triggers
  `Cache::forget()` + rebuild from the database.
- rebuilds the Eloquent `Collection` from the primitive array before
  returning, so the typed return signature `: Collection` still
  holds and existing call sites (`->contains('id', ...)`,
  `->contains('name', ...)`) keep working.
- still uses the same cache key `users.{id}.permissions`, so
  `AccessControlInvalidator::invalidateUser()` behavior is preserved.

Also during P0-3 work, `tests/Bootstrap.php` was fixed to drop a
stale `loadLaravelMigrations()` call — the in-repo migration already
creates `users`/`sessions` with `ulid` columns, which conflicted with
the default `bigIncrements` users table from Testbench. Without that
fix the entire Feature suite couldn't boot.

Regression tests in
`tests/Feature/Acl/PermissionCacheHardeningTest.php` (6 tests,
24 assertions):

- string garbage in cache is discarded and rebuilt
- int garbage in cache is discarded and rebuilt
- array with wrong shape is discarded and rebuilt
- forged `__PHP_Incomplete_Class` instance is discarded and rebuilt
- valid permissions round-trip through the primitive cache
- empty Collection signature is preserved for callers

`tests/Feature/Acl/AclServiceTest.php` continues to pass (7 tests,
8 assertions). Some pre-existing failures in
`HasRoleAndPermissionTest.php` and `RoleTest.php` are unrelated:
they assert integer permission/role IDs against the ULID schema
introduced by an earlier v7 migration and were broken before this
P0. Out of scope for the cache-hardening work.

---

## P0-2: Thunderclap end-to-end validation

**Status:** shipped, smoke-only (commit `86bf541b`).

Fresh app at `/tmp/laravolt-v7-thunderclap-smoke/`, built from a copy
of `laravel-starter-kit` with a Composer path repo to the local
laravolt core:

- `laravolt:install` + `migrate:fresh` clean
- Items table migration (id, name, decimal price, timestamps)
- `php artisan laravolt:clap --table=items --force` generated:
  - `modules/Item/Controllers/ItemController.php`
  - `modules/Item/Models/Item.php` + `ItemFactory.php`
  - `modules/Item/Requests/{Store,Update}.php`
  - `modules/Item/ItemServiceProvider.php` (Livewire + menu)
  - `modules/Item/ItemTableView.php` (Livewire, extends
    `Laravolt\Ui\TableView`)
  - `modules/Item/Tests/ItemTest.php`
  - `modules/Item/resources/views/{index,create,edit,show,_form}.blade.php`
  - `modules/Item/routes/web.php`
  - `modules/Item/config/item.php`

Generated tests (`pest modules/Item/Tests/`) pass 7/7 plus an added
`RenderingSmokeTest` that confirms the index renders with the unified
TableView (`data-role="suitable"`, `wire:id`, Livewire snapshot
referencing `modules.item.item-table-view`) and that no app-local
table partials are used.

**Stubs status:** no stub changes needed. Current stubs in
`packages/thunderclap/stubs/laravolt/` are aligned with Laravel 13,
Livewire 4, and the unified Preline TableView.

Artifact: `.scratch/v7-p0-thunderclap-smoke/`
- `REPORT.md` — summary
- `generated-index.blade.php` — the generated Blade
- `rendered-index.html` — captured HTML from authenticated GET
- `RenderingSmokeTest.php` — the rendering assertion

---

## P0-4: Starter-kit Docker templates

**Status:** shipped in starter kit (commit `19876b3`).

Templates land in `laravel-starter-kit`, not in the platform package.
Rationale: Docker files are app-shape, not library-shape; users see
them first in the starter kit when they run
`composer create-project laravolt/laravel-starter-kit`.

Files (in `laravel-starter-kit`):

- `docker/php/dev.Dockerfile` — PHP 8.4 CLI + Composer + Node + SQLite,
  runs `php artisan serve` for fast feedback. Adapted from VoltProcure
  dev image with starter-kit-friendly defaults (creates `.env` if
  missing, runs migrate, etc.).
- `docker/frankenphp/prod.Dockerfile` — FrankenPHP 1 / PHP 8.4 with
  baked Composer vendor + Vite assets, three build stages
  (vendor / assets / runtime). Uses `npm ci` when a `package-lock.json`
  is present, otherwise `npm install` — the starter kit ships
  `bun.lock` only, so the prod image documents this and falls back
  to `npm install` for image builds.
- `docker/frankenphp/Caddyfile` — `root /app/public`, gzip + zstd,
  `auto_https off` for proxy-fronted deployments.
- `docker-compose.dev.yml` — SQLite quickstart, source mounted at
  `/app`, optional Vite sidecar (commented).
- `docker-compose.prod.yml` — FrankenPHP-only by default, Postgres
  service commented in; storage / bootstrap-cache / database as
  named volumes; healthcheck against `/up`.
- `DOCKER.md` — image targets, usage, SQLite vs Postgres, bun/npm
  note.

We **do not** build & run the prod image in this Phase 2 task; that
is deferred to a release-side smoke test. `composer test` (pest,
54 passed) on the starter kit was confirmed green after the templates
landed.

---

## Out of scope / known issues (not P0 blockers for beta.1)

- `tests/Feature/Acl/HasRoleAndPermissionTest.php` and
  `tests/Feature/Acl/Models/RoleTest.php` have a pool of failures
  that assert integer IDs against the ULID schema. These were
  broken before this Phase 2 work and need a separate ULID-aware
  test sweep.
- `assertDatabaseHas` is unavailable on
  `Orchestra\Testbench\BrowserKit\TestCase`, which a couple of
  invalidator assertions use. Same pre-existing class.
- Starter kit `composer test` runs the full lint/types/coverage
  pipeline; we exercised `test:unit` (`pest --no-coverage`) since
  that's the gate the Docker templates can plausibly regress. Lint
  and types untouched by this change.
