## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2025-02-12 - Prevent N+1 select queries and array union data loss in ACL syncPermission
**Learning:** In PHP, using the array union `+` operator on numerically indexed arrays (e.g., `$arr1 + $arr2`) ignores values from the right-hand array if the index already exists in the left. This caused the `*` permission to be silently dropped during permission synchronization when `+ ['*']` was used. Additionally, looping over models to run `firstOrNew` introduces N+1 select queries which can be easily batched.
**Action:** Always use `array_merge()` or append via `$arr[] =` for mathematically combining standard arrays in PHP. For checking existing database items against an array of identifiers, use a single `whereIn()` query and map the collection by a case-insensitive key instead of querying in a loop.
