## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-30 - Optimized N+1 Query in Collection Mapping for firstOrCreate
**Learning:** In Laravolt, methods like `resolveRoleIds` or `syncPermission` often mapped over arrays calling `firstOrCreate` for string identifiers. Inside a loop, this triggered N+1 queries. We can resolve this bottleneck by batch fetching the existing records using `whereIn('name', $names)` first, comparing the differences, and only iterating for the missing entities.
**Action:** When implementing bulk synchronization of relationships from an array of identifiers in Laravel loops using `firstOrCreate()`, first collect the names, use `whereIn()` to find existing records, and use `array_diff` to isolate the missing names to create, thereby avoiding N+1 queries.
