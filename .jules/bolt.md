## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Bulk Operations in ACL Service
**Learning:** Laravolt's `Acl::syncPermission()` iteratively queried the DB for existing permissions (`firstOrNew`) and individually iterated to delete unused permissions (`$permission->delete()`). Since no complex cascading relies on `deleting` model events in this particular scenario, bulk fetching existing ones (`whereIn`) and bulk deleting unused ones (`toQuery()->delete()`) drops query count from O(N) to O(1) for massive syncs.
**Action:** Always look for iterative database calls inside synchronization loops and replace them with set-based operations (like bulk fetching and array diffing).
