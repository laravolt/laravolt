# Laravolt v7 Release TODO

## Current stable-release focus

- Keep Laravel 13-first onboarding green for Laravolt v7.
- Keep the VoltProcure Thunderclap demo working as the release pressure-test:
  - generated Item/Supplier/Department CRUD modules
  - existing `App\Models\*` enhancement
  - strict/final generated classes where practical
  - module factories for existing app models
  - ArchTest-friendly generated code
- Keep docs aligned with the demo so release readers can reproduce the flow quickly.

## Next iteration: laravel-starter-kit

Starter kit audit is intentionally deferred to the next iteration. Pick up from here:

- Fix Laravolt button component compatibility: starter-kit tests currently hit `Undefined variable $icon` in `resources/views/components/button.blade.php` when using current Laravolt from source.
- Re-run starter-kit gates after the component fix:
  - `composer test`
  - asset build (`bun install --frozen-lockfile`, `bun run build`)
  - targeted auth feature tests if failures remain
- Finalize starter-kit dependency strategy for the first beta:
  - use `^7.0@beta` after a beta tag exists
  - temporary local/source testing may use `dev-master as 7.0.0-beta.1`
- Keep README onboarding split clear:
  - `composer create-project` path
  - manual clone + `composer setup` path
  - browser/coverage as opt-in gates
- Ensure generated scaffolding from Laravolt v7 passes strict/security ArchTest in a fresh starter app.

## Before stable release

- Re-run VoltProcure full gates after any Laravolt changes.
- Re-run focused Thunderclap unit tests in Laravolt.
- Audit docs/examples for Laravel 13 / PHP 8.4 assumptions.
- Split commits/PRs so Thunderclap fixes, security cleanup, demo updates, and starter-kit work remain reviewable.
