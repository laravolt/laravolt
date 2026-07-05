## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-14 - Prevent N+1 queries during bulk user invalidation
**Learning:** In `AccessControlInvalidator`, validating users in a bulk iterable loop triggers individual database `DELETE` statements (to remove database sessions) for each user. It also checks `Schema::hasTable` and `Schema::hasColumn` on each loop iteration, resulting in many redundant Information Schema queries.
**Action:** When performing bulk deletion over an iterable collection of models, separate cache busting (which often doesn't natively support bulk invalidation well) from database queries. Collect primary keys into an array, then execute a single `whereIn()->delete()` and execute the Schema validation steps exactly once prior to the query.
