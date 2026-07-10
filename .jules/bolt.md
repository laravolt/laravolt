## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - Optimized Bulk Permission/Role Resolution (Collation Safety)
**Learning:** When resolving an array of permission/role names against the database to check for missing items, PHP's `array_udiff` with `strcasecmp` mimics MySQL's default case-insensitive collation but will break on case-sensitive databases like PostgreSQL. Instead, it is safer to map the database results to lowercase strings in a Collection and filter the input array. Also, take care when refactoring Laravel helpers like `str()` to facades like `Str::` without validating imports.
**Action:** Used `collect($input)->filter(fn($name) => !in_array(strtolower($name), $existingNames))` after querying to safely calculate diffs for missing role creations, and preserved the existing `str()->isUlid()` syntax.
