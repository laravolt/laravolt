## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-25 - Bulk Invalidations Require Centralized Cache Handling
**Learning:** When batching operations like `whereIn()->delete()` to solve N+1 problems inside loops, non-batchable operations like cache eviction (`Cache::forget`) often end up duplicating logic between singular (`invalidateUser`) and plural (`invalidateUsers`) methods.
**Action:** Always extract single-record side effects (like cache eviction) into a shared protected method when refactoring single-record models to support bulk processing. This keeps both code paths synchronized.
