## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-18 - Optimized Database Session Deletion in Loops
**Learning:** In Laravolt's `AccessControlInvalidator`, clearing database sessions inside a loop (via `invalidateUsers`) triggered repeated database queries, including expensive `Schema::hasTable` and `Schema::hasColumn` checks on every iteration.
**Action:** Batched the ID collection in the loop, kept the cache invalidation inside the loop, and moved the `deleteDatabaseSessions` call outside using an array and `whereIn()`. Also updated `deleteDatabaseSessions` to accept arrays or mixed types to remain backward compatible.
