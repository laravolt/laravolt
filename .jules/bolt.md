## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Bulk Database Session Deletion
**Learning:** `AccessControlInvalidator::deleteDatabaseSessions` performed an individual database deletion for each user to remove their sessions. In a large bulk operation, this caused N+1 database deletions and repeatedly executed `Schema::hasTable` and `Schema::hasColumn` check queries.
**Action:** Overrode `invalidateUsers` to accumulate an array of user IDs, extracted `Cache::forget` to a DRY helper, and passed the array down. Used `array_chunk` on the ids to prevent parameter binding limits, combined with `whereIn(...)->delete()` to optimize database deletions from O(N) queries down to O(1) batched queries.
