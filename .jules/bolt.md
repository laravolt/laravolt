## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2025-02-12 - Optimized Bulk Session Invalidation
**Learning:** `AccessControlInvalidator::invalidateUsers` originally invalidated sessions in a loop, resulting in repeated schema queries (`Schema::hasTable`, `Schema::hasColumn`) and multiple DELETE queries per user.
**Action:** Extract loop-based session deletion into a single array-based query using `whereIn()->delete()` while still invalidating cache keys iteratively. Extract cache invalidation to its own method for clarity.
