## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-05-28 - Optimized AccessControlInvalidator loop
**Learning:** When invalidating user sessions in Laravolt, `AccessControlInvalidator` previously checked the schema for the session table (O(N)) and executed a `delete()` query inside a foreach loop over user IDs (O(N)). While small loops are typically fast, this architecture triggers numerous schema queries that drastically drop performance under load.
**Action:** Batch schema checking to happen only once, and perform bulk deletion using `whereIn()` to eliminate N+1 query bottlenecks in invalidation loops.
