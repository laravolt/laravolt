## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - N+1 Queries in Bulk Operations and Array Union Trap
**Learning:** `Role::syncPermission()` created N+1 queries by executing `firstOrCreate` individually for every string permission passed. Furthermore, `Acl::syncPermission()` exhibited a subtle bug where the PHP array union operator `+` (`$permissions + ['*']`) ignored `'*'` because index 0 was already populated, potentially causing accidental deletion of the superadmin `'*'` permission.
**Action:** Always batch-fetch existing records using `whereIn()` before executing loops that create records. Never use the array union `+` operator for appending elements to numerically indexed arrays; use `[] =` or `array_merge()` instead.
## 2026-08-04 - Optimization: Batch Database Session Deletion to Prevent N+1 Checks
**Learning:** Checking database schemas `Schema::hasTable()` inside iterative structures triggering cascading operations silently introduces O(N) hidden database queries.
**Action:** When updating systems that depend on database meta-checks for multiple users (like invalidating sessions), accumulate the IDs and branch the data pipeline to execute single batched `whereIn()` queries with only a single schema resolution upfront.

## 2024-05-18 - Avoid bypassing Eloquent events for model deletions
**Learning:** While bulk deletion using `whereIn()->delete()` prevents N+1 queries, it completely bypasses Eloquent model events (`deleting`/`deleted`). In ACL packages, these events are almost always required to flush permission caches or handle cascading deletions in pivot tables. Bypassing them introduces high risk of stale caches or orphaned DB records.
**Action:** When deleting models where caching or pivot relation logic might be tied to their lifecycle events, always retain the model-level `$model->delete()` inside the loop unless you can manually handle cache invalidation and cascading.
