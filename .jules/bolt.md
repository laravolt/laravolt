## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-13 - [Optimize N+1 query in Role syncPermission]
**Learning:** When assigning permissions by an array of string names using a map/transform loop, doing a `firstOrCreate` on every item leads to an N+1 query problem, which is especially noticeable when assigning many permissions at once.
**Action:** Always pre-fetch the existing records in bulk using `whereIn` and array difference functions (`array_diff`), then only run `firstOrCreate` on the truly missing items, and combine the IDs together.
