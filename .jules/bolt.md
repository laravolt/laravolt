## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-06-18 - Acl Service Bulk Sync\n**Learning:** The Acl service creates a large N+1 query footprint when syncing permissions by checking each permission individually ( followed by ). And during cleanup, iterating through unused permissions and calling  on each causes N+1 deletes. Using bulk  queries resolves both.\n**Action:** Use  to fetch all matching permissions in a single query, key by name, and only create missing ones. Use  to batch delete unused permissions.
## 2026-06-18 - Acl Service Bulk Sync
**Learning:** The Acl service creates a large N+1 query footprint when syncing permissions by checking each permission individually (`firstOrNew()` followed by `save()`). And during cleanup, iterating through unused permissions and calling `delete()` on each causes N+1 deletes. Using bulk `whereIn()` queries resolves both.
**Action:** Use `whereIn()` to fetch all matching permissions in a single query, key by name, and only create missing ones. Use `whereIn()->delete()` to batch delete unused permissions.
