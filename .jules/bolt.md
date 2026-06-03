## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Bulk query existing relationships with firstOrCreate batching
**Learning:** In loops handling string names for models (like syncing permissions or roles), using `firstOrCreate` directly causes an N+1 issue. We must combine `whereIn` batching first and only loop missing ones through `firstOrCreate`, while handling string identifiers mapping efficiently.
**Action:** Extract string names, perform a single `whereIn('name', ...)` lookup, populate a map of name to primary keys, call `firstOrCreate` on missing elements dynamically, and pass the populated lookup to the main iterator.
