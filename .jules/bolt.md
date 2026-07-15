## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Cache Instantiated Permission Models During Request Lifecycle
**Learning:** In Laravolt's `HasRoleAndPermission` trait, the `permissions()` method fetched primitive cached arrays of permissions and mapped them to NEW `Permission` Eloquent models inside a new `Collection` every single time it was called. Because UI checks like `hasPermission()` call this repeatedly, a page rendering 100 permission checks would instantiate thousands of duplicate Model instances unnecessarily, causing large memory allocations and GC pressure.
**Action:** Introduced an in-memory class property (`protected $permissionsCacheCollection`) to cache the instantiated `Collection` of models for the duration of the object lifecycle. Added cache-busting to `invalidateAccessControl()` so changes take effect immediately.
