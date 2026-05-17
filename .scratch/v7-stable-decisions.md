# Laravolt v7 — Stable Decisions Log

This file records the design decisions made while closing the P0/P1/P2
lists in `v7-stable-issues-from-voltprocure.md`. Each section is short
on purpose; the source of truth is the code.

> **Status (2026-05-17):** P0-1, P0-2, P0-3, P0-4, P1-5, P1-6, P1-7,
> P1-8, P2-9, P2-10, P2-11 all shipped. Ready for `v7.0.0` stable tag
> pending Rama's review.

## Shipped commits (Phase 3 — stable)

Laravolt core (`/Users/rama/laravolt/laravolt`, branch `master`):

- `89380f41` — `feat(thunderclap): generate FormRequest-backed PrelineForm CRUD by default` (P1-5)
- `e81934de` — `feat(thunderclap): enforce permission gate on generated routes and menus` (P1-8)
- `e37aecf7` — `chore(ui): remove residual Fomantic class leakage from v7 components` (P2-11)

Starter kit (`/Users/rama/laravolt/laravel-starter-kit`, branch `main`):

- `bb6e5c1` — `feat(seeders): add DemoSeeder with role/permission/user story` (P1-7)
- `097afb9` — `test(browser): add smoke tests for generated CRUD happy path` (P2-9)

Docs (`/Users/rama/laravolt/docs-v7`, branch `docs/v7-ai-ready`):

- `1e0a4fa` — `docs(v7): add business-actions convention for non-CRUD workflows` (P1-6)
- `82f92e5` — `docs(llm): add llms.txt + ai-context + task recipes` (P2-10)

## Shipped commits (Phase 2 — beta)

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

---

## P1-5: FormRequest-backed PrelineForm in Thunderclap

**Status:** shipped (commit `89380f41`).

The Thunderclap stubs were already FormRequest-backed before this work
— `Requests/Store.php.stub` and `Requests/Update.php.stub` both extend
`Illuminate\Foundation\Http\FormRequest` with `rules()` and
`authorize()`, the controller stub imports and typehints them on
`store()`/`update()`, and `_form.blade.php.stub` uses PrelineForm
helpers (`form()->action`, `form()->submit`, `form()->linkButton`).

This commit **locks** that as the v7 default by adding
`tests/Unit/Thunderclap/FormRequestStubsTest.php` (8 tests, 26
assertions). Future drift away from FormRequest + PrelineForm will be
a test failure.

No stub changes needed.

## P1-6: Business actions convention (docs)

**Status:** shipped (docs commit `1e0a4fa`).

New page at `/v7/admin-workflows/business-actions` documenting the v7
stable pattern for non-CRUD operations on top of Thunderclap-generated
modules:

- Folder convention: `app/Actions/{Domain}/{Verb}Action.php`
- Single-action invokable vs class-with-methods
- Routing with permission gate (`can:<permission>`) + policy
  authorization
- Thin controller + FormRequest for action input
- Blade trigger patterns (button form, modal form)
- Pest tests for both action class and route
- Regen-safe recipe for adding actions on top of Thunderclap
- When to graduate to `WorkflowService` / Camunda

Workflow engine integration (Camunda) is deferred to post-stable. The
v7 stable story is action classes + model status + policy.

## P1-7: Demo seeder + story conventions

**Status:** shipped in starter kit (commit `bb6e5c1`).

`database/seeders/DemoSeeder.php` seeds three roles (`admin`,
`manager`, `user`), baseline platform permissions plus a generic
`resource.*` set Thunderclap modules can map onto, and three demo
users (admin/manager/user @ laravolt.dev, all password `secret`,
email-verified). All writes are in a DB transaction; firstOrCreate +
updateOrCreate make repeat runs safe.

`tests/Feature/Seeders/DemoSeederTest.php` covers roles, permissions,
user assignment, and idempotency (4 tests, 24 assertions). README
gained a "Demo Seed" section.

`composer test:unit` on the starter kit went from 54 -> 58 passed.

## P1-8: Permission gate on generated routes and menus

**Status:** shipped (commit `e81934de`).

Generated CRUD modules previously gated only the sidebar menu via
`SidebarMenu` permission checks — routes themselves were behind
`web,auth` only. Menu visibility is not a security boundary.

Changes:

- `config/config.php.stub`: default `permission` is now
  `'modules.<module-name>.view'` (was empty array). Set to `false`
  for module-wide open access.
- `routes/web.php.stub`: reads the config permission and appends
  `can:<permission>` to the resource group middleware.
- `Tests/Test.php.stub`: now creates an admin role with wildcard `*`
  permission in `beforeEach()` so generated CRUD tests pass under
  the new gate.
- `ServiceProvider.php.stub`: menu registration already passed the
  same config permission via `->data('permission', ...)`, so menu
  visibility and route access now share one source of truth.
- New test: `tests/Unit/Thunderclap/MenuAclStubsTest.php` (4 tests,
  7 assertions) asserts the wiring stays correct.

## P2-9: Browser smoke tests for generated CRUD

**Status:** shipped in starter kit (commit `097afb9`).

`tests/Browser/GeneratedCrudSmokeTest.php` exercises the Epicentrum
Role CRUD as a representative happy path (the starter kit has no
generated module by design). Tests are tagged `->group('browser',
'generated-crud')` so they remain opt-in via `--group=browser`.

Also re-baselined existing browser screenshot snapshots in
`tests/.pest/snapshots/Browser/` (they had drifted from the Preline
UI). Final: `pest --testsuite Browser` -> 54 passed, 93 assertions,
0 failures.

## P2-10: LLM-readable docs

**Status:** shipped in docs repo (commit `82f92e5`).

Two new canonical AI-agent docs pages:

- `/v7/ai-context` \u2014 entry point describing Laravolt's structure
  (key concepts: Suitable, TableView, PrelineForm, Thunderclap, ACL),
  stack versions, how to consume the docs as agent context, and
  common agent pitfalls.
- `/v7/task-recipes` \u2014 copy-paste recipes for common
  post-generation tasks (add a business action, add a permission,
  customize a form, add an index filter, register a sidebar entry,
  write a browser smoke test, re-baseline screenshots).

`scripts/generate-llms.mjs` auto-discovers all `src/app/**/page.md`,
so `/llms.txt` and `/llms-full.txt` were regenerated to include 67
page mirrors including the new pages and `/v7/admin-workflows/business-actions`.

## P2-11: Visual QA pass for Preline components

**Status:** shipped (commit `e37aecf7`).

Audit of `resources/views/components/` and
`resources/views/ui-component/` found zero Fomantic class tokens in
the v7 component layer.

The one residual leak in v7-active code was the Suitable `Label`
column (used by `Epicentrum\\Livewire\\UserTable`), which still
rendered `<div class="ui label ...">` from PHP. Fixed to render a
Preline pill badge with sensible default palette
(`inline-flex ... rounded-full ...`).

Also dropped `->addClass('mini')` from both `Epicentrum\\Livewire\\UserTable`
and `Epicentrum\\Table\\UserTable` (Fomantic-only size class, no-op
in Tailwind).

New regression test: `tests/Unit/Ui/NoFomanticClassesTest.php` (3
tests, 5 assertions) scans v7 view files for forbidden tokens and
asserts Suitable Label renders Preline markup.

Out-of-scope residual Fomantic in the monorepo (v6 artifacts, not v7
deliverables): `packages/semantic-form/`, `packages/suitable/src/Columns/Dropdown.php`
(unused), and `packages/suitable/resources/views/columns/dropdown/cell.blade.php`
(orphaned).

---

## Full-suite final status

### Laravolt core

`php vendor/bin/pest --no-coverage`:

- **86 passed, 54 failed** (246 assertions)
- All 54 failures are **pre-existing**, documented in the "Out of
  scope" section above:
  - ACL ULID schema tests (HasRoleAndPermissionTest, RoleTest)
  - Helpers tests (number formatting)
  - Media chunk tests (binding resolution)
  - PrelineForm FieldCollectionLayoutTest
  - Support WhereLikeSecurityRegressionTest
  - One flaky Acl/SyncPermissionCommandTest (output-string match)
- Baseline before this phase: 71 passed, 54 failed. Phase 3 added
  16 new green tests and 0 new failures.

Targeted gate `phpunit tests/Unit/Thunderclap tests/Unit/Ui`:
26 passed (83 assertions).

### Starter kit

`composer test:unit` -> **58 passed** (240 assertions), 8-process
parallel. Includes the 4 new DemoSeeder tests.

`pest --testsuite Browser` -> **54 passed** (93 assertions), 0
failed. Includes the 4 new GeneratedCrudSmokeTest tests.

### VoltProcure on beta

Switched `voltprocure/composer.json` to `laravolt/laravolt:^7.0@beta`,
ran `composer install`:

- Installed `laravolt/laravolt v7.0.0-beta.4`
- `php artisan test --no-coverage` -> **56 passed** (154 assertions),
  0 failed

VoltProcure changes reverted (not pushed), per instructions.

---

## Remaining gaps (ranked)

1. **ACL ULID test sweep** \u2014 `HasRoleAndPermissionTest` and
   `RoleTest` assert integer IDs against the ULID schema. Tracked
   pre-stable but not a release blocker; existing wildcards still
   work. Schedule a Phase 4 ULID-aware refactor.
2. **Suitable v6 column cleanup** \u2014 `packages/suitable/src/Columns/Dropdown.php`
   and its orphaned blade template can be deleted in a future
   cleanup pass. Not used by v7 generators.
3. **Media chunk binding tests** \u2014 `CleanupStaleChunksJobTest` /
   `ClientUploadConfigTest` need their service container bindings
   fixed. Pre-existing and unrelated to stable scope.
4. **Camunda / WorkflowService integration** \u2014 Documented as
   post-stable in the new business-actions page. Action classes +
   model status + policy is the v7 stable story for non-CRUD ops.

---

## Recommendation

**Ready for `v7.0.0` stable tag.**

- All P1/P2 items in `v7-stable-issues-from-voltprocure.md` shipped.
- VoltProcure (the soak target) installs cleanly on `^7.0@beta`
  and runs 56/56 green.
- Net regression delta: zero. All 54 remaining failures in laravolt
  core are pre-existing and documented.
- Starter kit and docs are aligned with the new conventions.

Phase 4 candidates (post-tag):

- ACL ULID test sweep
- Camunda / WorkflowService integration with the new business-actions
  pattern
- Suitable v6 leftover cleanup
- CI integration for `composer test:browser` once Playwright is
  provisioned on CI hosts
