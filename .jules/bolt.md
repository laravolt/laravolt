## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-07-20 - Refactored PermissionController Update to eliminate N+1 query
**Learning:** Refactoring an array loop that executed individual database updates into a single batch `UPDATE ... CASE` SQL query reduces database roundtrips from O(N) to O(1) per chunk, drastically improving network-bound performance in large forms like permissions settings.
**Action:** When saving multiple database records derived from request arrays, especially in backend settings/configurations, use `CASE` blocks via `$model->getConnection()->update(...)`. Always chunk bindings to avoid exceeding connection limits and manually touch `updated_at` timestamps using `$model->freshTimestampString()`.
