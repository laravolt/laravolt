## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-25 - Bulk UPDATE Query Builder with Laravel
**Learning:** `PermissionController::update` was calling `config('laravolt.epicentrum.models.permission')::whereId($key)->update(...)` in a `foreach` loop, resulting in `N+1` queries for bulk permission updates. By extracting `request('permission', [])` and chunking it, we can resolve the model instance, extract the connection and grammar, and craft a `CASE` statement inside a raw query (`UPDATE ... SET ... = CASE WHEN ...`). Since Eloquent `update` auto-updates `updated_at`, it must be manually appended to the raw query (`usesTimestamps` and `freshTimestampString()`).
**Action:** When updating a large number of rows iteratively, refactor to chunking and `CASE` conditional updates via the raw Query Builder instance to ensure efficiency, keeping constraints on table and column wrappings via the DB grammar.
