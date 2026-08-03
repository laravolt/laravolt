## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - Bulk Permission Sync N+1
**Learning:** In `Laravolt\Platform\Services\Acl`, generating missing permissions in the `syncPermission` method originally used a loop that fired `firstOrNew` for every permission, resulting in an O(N) database query bottleneck. Furthermore, `+` array union was incorrectly dropping the `*` permission when appending it to numerically indexed permissions array.
**Action:** Always batch fetch using `whereIn` combined with `keyBy(fn ($item) => strtolower($item->name))` for in-memory mapping before falling back to `firstOrCreate`. Never use `+` for numerically-indexed array concatenation.
