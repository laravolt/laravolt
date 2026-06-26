## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-18 - Bulk Database Invalidations with Schema Checks
**Learning:** Found an N+1 query loop in `AccessControlInvalidator@invalidateUsers`. The loop issued individual `deleteDatabaseSessions` calls for each user, which in turn performed `Schema::hasTable` checks and a database `delete()` query for every single item.
**Action:** Always extract loop logic that contains schema/database queries. Group IDs in the loop, execute cache removals (as they aren't easily bulked) in the loop, but then execute a single `whereIn()->delete()` operation for the database deletion outside the loop to significantly minimize database traffic and execution time.
