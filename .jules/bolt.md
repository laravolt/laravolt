## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - Optimized Array Contains Check via Dict Mapping
**Learning:** In Laravel Collections containing objects/models, checking an array of items via iterative `$collection->contains()` with closures or string properties is O(M*N). Pre-computing a lookup dictionary using `$collection->keyBy(fn($i) => strtolower($i->name))` drops the check time dramatically (from ~23s to ~3s for 10000 iterations over 100 items), as dictionary `->has()` lookups are O(1).
**Action:** Implemented caching dict lookup inside array recursive loops within `_hasPermission` for `Role` and `HasRoleAndPermission` to drastically optimize checking multiple string-based permissions.
