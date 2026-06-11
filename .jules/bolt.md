## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-23 - Batching Database Session Cleanup
**Learning:** Checking schema (`Schema::hasTable`, `Schema::hasColumn`) inside a loop over multiple user models results in numerous N+1 database metadata queries. In the case of `AccessControlInvalidator::invalidateUsers`, this scales linearly with the number of users invalidated.
**Action:** Extract DB IDs and run bulk `whereIn()->delete()` logic. Collect target items first, and push the array structure down the callstack where it can be handled in a single batch.
