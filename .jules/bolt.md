## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-19 - N+1 Optimizations with Model Events
**Learning:** When bulk-deleting models to optimize an N+1 query problem, using `$query->whereIn(...)->delete()` bypasses Eloquent model events. In packages like Acl that rely on cache invalidation during `deleting`/`deleted` events, this can lead to stale caches.
**Action:** When evaluating bulk deletions, check if related cache invalidation or other critical business logic depends on model events. If so, iterate and call `$model->delete()` individually, accepting the query overhead, or implement a manual bulk cache flush. Also, remember that `array_merge` correctly appends elements to numerically indexed arrays, while the `+` operator ignores keys from the right array that exist in the left.
