## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-24 - Bulk Permission Updates
**Learning:** Permission update loops can hit N+1 update bottlenecks. You can optimize this by switching from Eloquent looping updates to a single bulk SQL query using a `CASE` statement. When doing this, be sure to update `updated_at` manually since the raw SQL bypasses Eloquent lifecycle events. Chunking prevents exceeding DB parameter limits.
**Action:** Use SQL `CASE` statements constructed safely with grammar and bindings to batch update multiple records in high-throughput endpoints.
