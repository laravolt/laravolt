## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-07-14 - Prevent unnecessary database queries when ID lists are empty
**Learning:** When refactoring loops to aggregate IDs for bulk operations like `whereIn()`, executing the query with an empty array can sometimes lead to unnecessary database round-trips or syntax errors depending on the driver.
**Action:** Always wrap the bulk database execution block in an `! empty($ids)` check to explicitly short-circuit if no valid targets were extracted.
