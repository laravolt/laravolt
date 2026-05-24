## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-24 - Bulk String Model Lookups Should Match DB Collation Sensitivity
**Learning:** When optimizing N+1 string name lookups (e.g. roles, permissions) by loading them into an in-memory Laravel Collection via `keyBy('name')`, PHP treats string keys as case-sensitive. However, the database might be case-insensitive (like MySQL default). This can cause an existing item to be missed and re-created (throwing constraint errors) if the case differs.
**Action:** When keying a Collection by a string identifier for existence checks, use `->keyBy(fn($item) => strtolower($item->name))` and check with `has(strtolower($name))` to simulate case-insensitive collation in PHP memory.
