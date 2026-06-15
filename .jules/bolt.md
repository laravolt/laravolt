## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2025-06-15 - Dynamic timestamps in raw SQL updates
**Learning:** When bypassing Eloquent using raw SQL (e.g. `CASE` statements for bulk updating), relying on hardcoded `updated_at` column names and formats breaks when models customize their timestamp columns.
**Action:** Always fetch the column name dynamically using `$model->getUpdatedAtColumn() ?? 'updated_at'` and generate the timestamp using `$model->freshTimestampString()` before executing the raw query.

## 2025-06-15 - Pest Trait Collision in Testbench
**Learning:** Attempting to use a custom Testbench setup trait (like `Laravolt\Tests\Bootstrap` with its own `setUp` method) inside a Pest feature test via `uses()` throws a collision error with `Pest\Concerns\Testable::setUp`.
**Action:** In these cases, fall back to writing standard PHPUnit tests extending `Orchestra\Testbench\TestCase` to successfully import and use the custom bootstrapping trait.
