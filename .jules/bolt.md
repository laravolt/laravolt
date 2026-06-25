## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Bulk Database Session Deletion
**Learning:** In Laravolt `AccessControlInvalidator`, deleting sessions for an array of users triggers an N+1 query (`where('user_id', $id)->delete()`). Refactoring this to gather all user IDs first and use a single `whereIn('user_id', $ids)->delete()` eliminates the bottleneck. Also, Laravel `Cache::forget` natively does not support bulk invalidation for keys with different variables, so we must loop for caches while bulk querying for the database.
**Action:** Extract user IDs within a loop to clear caches and then execute a single database operation (`whereIn()->delete()`) when bulk operations combine caching and database mutations. Always use `is_iterable` in protected DB abstraction functions to keep backwards compatibility.
