## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-07-11 - Bulk Fetching Relationships by String Name
**Learning:** Laravolt's `syncPermission` (in `Role.php`) and `resolveRoleIds` (in `HasRoleAndPermission.php`) iterated over arrays of roles/permissions strings and used `firstOrCreate()` per item. This causes an N+1 query bottleneck. While bulk insertions are possible, `firstOrCreate` triggers model events and ensures logic continuity, but reading existence can be batched to avoid query spam.
**Action:** Always batch fetch existing records by name using `whereIn('name', $names)->get()->keyBy(fn($p) => strtolower($p->name))` first, then do in-memory existence checks (via case-insensitive matching). Only iterate and call `firstOrCreate` for records that are genuinely missing to solve O(N) database reads.
