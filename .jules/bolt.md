## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-06-28 - Bulk Permission and Role Syncing Optimization
**Learning:** In Laravolt Platform, arrays of roles or permissions provided as strings were individually resolved via `$model->firstOrCreate()` inside `.map()` collections, creating O(N) database queries per synchronization call.
**Action:** When resolving arrays of identifiers/strings for relationships in loops, always perform a `whereIn` batch query first to fetch existing records. Iterate via hash-map lookup (keyed via `strtolower` to handle DB colation) inside the loop, and only use `firstOrCreate` for missing records to eliminate the N+1 query bottleneck.

## 2024-06-28 - Avatar Alignment Fix
**Learning:** `laravolt/avatar` package passes the invalid Enum value 'middle' (rather than 'center') for vertical alignment to the `intervention/image` ^4.0 library during avatar generation, triggering an exception (`Invalid value for alignment`) on views using `$user->avatar`.
**Action:** Always verify `vendor` package parameter mappings when a major upstream library like `intervention/image` switches from string arguments to strict Enumerations. Modify `vendor/laravolt/avatar/src/Avatar.php` to use the correct enum `align('center', 'center')`.
