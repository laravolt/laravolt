## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2024-05-23 - Bulk query N+1 fix for assigning/syncing string-based IDs
**Learning:** When resolving string IDs to models in a loop (`firstOrCreate` inside `map`), it executes a `SELECT` and `INSERT` query for every single item, resulting in an O(n) N+1 bottleneck. This is common when assigning multiple roles or permissions via array of string names (e.g. `$user->assignRole(['admin', 'editor', 'author'])` or `$role->syncPermission(['create', 'read', 'update'])`).
**Action:** Extract all string names into an array, query the database once with `whereIn('name', $names)->get()->keyBy('name')` to fetch existing records. Then iterate the array again: if it exists in the fetched collection, use its key; if not, create it. This reduces the DB lookups from O(n) to O(1).
