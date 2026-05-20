## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-20 - Optimized Role/Permission String Syncing
**Learning:** `firstOrCreate` in loops (like in `syncPermission` and `resolveRoleIds`) creates massive N+1 query bottlenecks when resolving string arrays to database IDs.
**Action:** Always extract string names first, perform a bulk `whereIn('name', $names)->get()` to fetch existing models, and only loop and `firstOrCreate` for the missing string names.
