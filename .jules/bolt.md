## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2024-05-18 - Bulk Operations in ACL syncPermission
**Learning:** In `Laravolt\Platform\Services\Acl`, the `syncPermission` iterated over registered permissions to check existence via `firstOrNew` and save them. Not only did this generate N+1 queries, but using `firstOrNew` followed by `save()` in a loop without catching duplicates opens up race conditions resulting in unique constraint violations in concurrent workflows. Additionally, array union operations (`+`) can swallow indexes if an explicit index collides with sequential indexes during merge operations, such as adding the wildcard `['*']`.
**Action:** Always refactor model initialization loops in ACL systems to bulk fetch existing records into a keyed Collection first. When persisting missing records based on names, prefer using `firstOrCreate` over `firstOrNew` + `save` to leverage Laravel's built-in transaction safe-guards. Always use `array_merge` over the `+` operator when merging flat arrays in Laravel PHP.
