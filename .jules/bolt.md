## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Batch fetch relational entities before firstOrCreate inside loops
**Learning:** In sync algorithms (like syncing permissions or roles from string names), looping and executing `firstOrCreate` individually for each string causes O(N) database queries.
**Action:** Extract all target string names from the array first, perform a single `whereIn` bulk fetch keyed by lowercase name, and only call `firstOrCreate` on items missing from the pre-fetched collection. This reduces database queries to O(1) for existing entities.
