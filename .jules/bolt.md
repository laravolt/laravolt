## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Access Control Invalidator
**Learning:** Laravolt's `AccessControlInvalidator` calls `deleteDatabaseSessions` for every user when invalidating caches. `deleteDatabaseSessions` performs `Schema::hasTable` and `Schema::hasColumn` on every call, running extremely slow queries against information schema per loop iteration.
**Action:** Extract cache-invalidation into a batch of IDs, run schema checks only once via memoization, and use a `whereIn` delete instead of individual delete statements.
