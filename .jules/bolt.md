## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2024-05-18 - Acl permission sync optimization
**Learning:** In Laravolt Platform's ACL implementation (`src/Platform/Services/Acl.php`), the `syncPermission` method iteratively queried the database (`firstOrNew()` followed by conditional `save()`) and similarly looped over models to delete missing permissions one by one, creating a significant N+1 query bottleneck.
**Action:** Replace looped existing-record checks with a single `whereIn()` query that maps to an array, process missing models in memory, and loop only for `create()`. Then collect all missing IDs and run a single bulk `whereIn()->delete()` for cleanup. This transforms the operation from O(N) queries to O(1) queries.
