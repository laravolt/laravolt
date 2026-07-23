## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - Optimized Bulk Assignment of Roles and Permissions
**Learning:** Laravolt's `syncPermission` and `syncRoles` methods historically iterated over arrays of strings and invoked `firstOrCreate` on each string inside the collection map. This caused N+1 database queries when assigning multiple roles or permissions by name.
**Action:** Overhauled `resolveRoleIds` and `syncPermission` to implement a bulk `whereIn` fetch strategy. All string names are collected, queried once, mapped to a lookup array, and only genuinely missing records fall back to individual `firstOrCreate` statements. This dramatically reduces DB overhead from `O(n)` to `O(1)` queries in most standard operational paths.
