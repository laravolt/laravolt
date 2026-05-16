## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-18 - Bulk Invalidation of Access Controls
**Learning:** In Laravolt Platform, invalidating access controls for multiple users (like when a role is modified) triggers an N+1 query problem because `AccessControlInvalidator::invalidateUsers` iteratively calls `invalidateUser`, which sequentially runs `delete` queries on the database session table.
**Action:** Extract the IDs in the iteration and perform a single `whereIn` query with `delete()` to resolve the N+1 problem.
