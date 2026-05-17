## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-17 - Optimize Bulk Session Invalidation
**Learning:** In Laravolt, invalidating multiple users at once via `AccessControlInvalidator::invalidateUsers` originally called `invalidateUser` iteratively. This resulted in an N+1 query issue for deleting database sessions, while caching must still be done iteratively.
**Action:** Extract the target IDs into an array during iteration to clear cache sequentially, then execute a single `whereIn()->delete()` query on the session table to optimize database interaction.
