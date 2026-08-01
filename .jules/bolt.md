## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-08-01 - Array Merge Bug in Acl syncPermission
**Learning:** Using PHP's array union operator  on numerically indexed arrays (e.g., ) ignores values from the right-hand array if the index already exists in the left array, which led to a bug failing to merge the '*' wildcard in `syncPermission`.
**Action:** Always use `array_merge()` or append via `$arr[] =` when merging numerically indexed arrays.
## 2024-05-11 - Array Merge Bug in Acl syncPermission
**Learning:** Using PHP's array union operator `+` on numerically indexed arrays (e.g., `$arr1 + $arr2`) ignores values from the right-hand array if the index already exists in the left array, which led to a bug failing to merge the '*' wildcard in `syncPermission`.
**Action:** Always use `array_merge()` or append via `$arr[] =` when merging numerically indexed arrays.
