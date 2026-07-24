## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-07-24 - O(1) Bulk Fetching for Eloquent Models with whereIn
**Learning:** Resolving IDs from strings inside iterative collections (using map/transform) causes severe N+1 query bottlenecks when using firstOrCreate() to guarantee existence. Additionally, PHP array differences with strict casing can cause bugs, so map names through strtolower.
**Action:** Extract unique string targets, run a single whereIn query to build a dictionary mapping case-insensitive names to IDs. Calculate missing records and run firstOrCreate only on the delta, maintaining Eloquent events safely while preventing duplicate insert constraints.
