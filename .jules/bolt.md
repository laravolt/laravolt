## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2024-05-11 - Bulk Relationship Querying for ACL
**Learning:** In `HasRoleAndPermission` and `Role` models, syncing relationships with arrays of string identifiers caused an N+1 query problem by invoking `firstOrCreate` on every iteration.
**Action:** Extract string identifiers into a separate array, query them in bulk via `whereIn`, and index them using a case-insensitive Collection (`keyBy(fn ($item) => strtolower($item->name))`). Look up from the collection inside the loop.
