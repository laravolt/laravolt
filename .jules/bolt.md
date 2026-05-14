## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2025-02-12 - Optimized N+1 Query in Role and Permission Assignments
**Learning:** In Laravolt Platform, syncing arrays of new string permissions or roles via `syncPermission` and `assignRole`/`syncRoles` triggers individual `firstOrCreate` or `where(...)->first()` database queries for each string element, causing an N+1 query performance hit.
**Action:** Implemented a bulk `whereIn` lookup to fetch all existing role/permission names before mapping over the list. Retrieved records are cached in a Collection and checked in memory, deferring to `firstOrCreate` only for completely new entries, dropping DB queries from O(N) to O(1) for existing entities.
