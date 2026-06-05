## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Bulk Database Session Deletion Optimization
**Learning:** In `Laravolt\Platform\Services\AccessControlInvalidator`, deleting sessions inside a loop when invalidating multiple users creates a hidden N+1 query bottleneck since each user invalidation triggered a separate `DELETE` query on the session table. Also, since Laravel Cache does not natively support bulk `forget()`, iterating through `Cache::forget` remains necessary while we bulk-optimize the database operation using `whereIn()->delete()`.
**Action:** Overrode `invalidateUsers` to accumulate user IDs in an array, loop over `Cache::forget` individually, and then perform a single `whereIn('user_id', $userIds)->delete()` to optimize database round-trips from O(N) to O(1).
