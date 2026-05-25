## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Bulk Delete Database Sessions
**Learning:** Checking the database schema (`Schema::hasTable`, `Schema::hasColumn`) inside a loop incurs significant N+1 hidden queries to the information_schema, degrading performance severely during batch updates. Extracting these checks out of loops is critical.
**Action:** When performing bulk cache/db cleanups (like session expirations), collect IDs in a loop and execute a single `whereIn()->delete()` with the schema check executed once beforehand.
