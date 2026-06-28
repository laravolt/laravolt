## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-28 - Bulk Permission and Role Syncing Optimization
**Learning:** In Laravolt Platform, arrays of roles or permissions provided as strings were individually resolved via `$model->firstOrCreate()` inside `.map()` collections, creating O(N) database queries per synchronization call.
**Action:** When resolving arrays of identifiers/strings for relationships in loops, always perform a `whereIn` batch query first to fetch existing records. Iterate via hash-map lookup (keyed via `strtolower` to handle DB colation) inside the loop, and only use `firstOrCreate` for missing records to eliminate the N+1 query bottleneck.
