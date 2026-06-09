## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-09 - N+1 Optimizations vs Events
**Learning:** While optimizing an N+1 bug using query builder bulk deletes (e.g. ) avoids loops, it bypasses important Eloquent model events (/). These events are critical in ecosystems like Laravolt/Spatie for flushing the permission caches or firing custom listeners.
**Action:** Always retain individual model loops when invoking  for entities that rely on model events for cache invalidation (like  or ), or fallback to  on the Eloquent model instead of the Query Builder.
## 2024-06-09 - N+1 Optimizations vs Events
**Learning:** While optimizing an N+1 bug using query builder bulk deletes (e.g., `whereIn()->delete()`) avoids loops, it bypasses important Eloquent model events (`deleted`/`deleting`). These events are critical in ecosystems like Laravolt/Spatie for flushing the permission caches or firing custom listeners.
**Action:** Always retain individual model loops when invoking `->delete()` for entities that rely on model events for cache invalidation (like `Permission` or `Role`), or fallback to `destroy()` on the Eloquent model instead of the Query Builder.
