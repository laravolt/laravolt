## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2024-05-18 - Bulk Invalidation of Database Sessions
**Learning:** Invalidation routines (like `AccessControlInvalidator::invalidateUsers`) that loop through objects to individually perform query deletions (like session purging) lead to hidden N+1 queries. When batch-deleting database sessions across a large number of users, extracting the IDs and executing a single `whereIn()->delete()` is significantly faster.
**Action:** Always inspect array iterations that trigger database operations for potential batch-processing opportunities, such as extracting IDs and utilizing `whereIn()`.
