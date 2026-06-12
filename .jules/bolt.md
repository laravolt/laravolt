## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-18 - Optimized N+1 Query in ACL Sync and Fixed Array Union Bug
**Learning:** During permission sync, fetching existing records via `firstOrNew` inside a loop creates an N+1 query problem. Pre-fetching all expected records into a keyed Collection (using `strtolower` to prevent case mismatches) turns O(N) `SELECT` queries into O(1). Additionally, PHP's array union (`+`) ignores values from the right-hand array if the numeric index exists on the left-hand array, which can inadvertently skip items (like wildcard permissions) when merging.
**Action:** Always pre-fetch records into a Collection (`whereIn(...)->get()->keyBy(...)`) when synchronizing relationships or iterating through names. Avoid using array unions (`+`) with numerically indexed arrays; instead, use `array_merge` or `$array[] = 'value'`.
