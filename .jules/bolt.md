## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - ACL Event Dispatch Safety During Bulk Delete
**Learning:** While `whereIn()->delete()` correctly solves N+1 delete queries, Laravolt's ACL caching heavily depends on Eloquent events like `deleting`/`deleted` being fired on the `Permission` model to accurately invalidate permission caches and detach related roles/users. If bulk deletion bypasses these events, the system will retain stale cache data and orphaned pivot entries.
**Action:** When optimizing loop deletions in highly integrated systems like ACL packages, prioritize correct event dispatch over raw bulk-query performance. Iterative model deletion is required unless mass cache invalidation is explicitly implemented as a post-action.
