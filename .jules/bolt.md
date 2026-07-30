## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-07-30 - Array Union Operator  Discards Data When Merging Numerically Indexed Arrays
**Learning:** In PHP, using the array union  operator on numerically indexed arrays (e.g., `$permissions = $this->permissions() + ['*'];`) does not behave like `array_merge`. It ignores the right-hand array values if the index already exists in the left array, causing subtle bugs like dropping wildcards.
**Action:** Use `array_merge()` or append directly via `$array[] = 'value'` when merging or adding to numerically indexed arrays.
## 2023-11-20 - Array Union Operator `+` Discards Data When Merging Numerically Indexed Arrays
**Learning:** In PHP, using the array union `+` operator on numerically indexed arrays (e.g., `$permissions = $this->permissions() + ['*'];`) does not behave like `array_merge`. It ignores the right-hand array values if the index already exists in the left array, causing subtle bugs like dropping wildcards.
**Action:** Use `array_merge()` or append directly via `$array[] = 'value'` when merging or adding to numerically indexed arrays.
