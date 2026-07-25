## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - AccessControlInvalidator N+1 schema checks
**Learning:** Laravolt`s `AccessControlInvalidator` calls `invalidateUser` in a loop inside `invalidateUsers`. `invalidateUser` calls `deleteDatabaseSessions` per user, which performs `Schema::hasTable` and `Schema::hasColumn` reflection checks per database iteration, resulting in N+1 schema reflection queries.
**Action:** Extract database schema reflection checks and deletion queries out of loops. When batched invalidation is required, use `is_iterable` and `whereIn()->delete()` to consolidate operations and avoid repetitive query builder invocations.
