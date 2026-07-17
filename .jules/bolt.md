## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-19 - Bulk resolution of roles and permissions to fix N+1 in sync loops
**Learning:** In Laravolt's `HasRoleAndPermission` trait and `Role` model, bulk assigning roles/permissions from an array of strings triggers N+1 database queries via `firstOrCreate` or `first` loops.
**Action:** Extract the strings, use a single case-insensitive `whereIn` query to fetch existing records en masse, map them via `strtolower()`, and only iterate with `firstOrCreate` for the missing names to collapse N+1 lookups into O(1).
