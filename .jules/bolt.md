## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2026-07-18 - [Resolve N+1 in Permission Updates]
**Learning:** Updating permission descriptions inside a loop triggers N+1 queries. We can optimize this by collecting the updates and executing a single bulk UPDATE query with a CASE statement, chunking the queries to respect SQLite parameter limits. Raw queries bypass Eloquent's timestamp updates, so it's necessary to manually resolve the `updated_at` column using grammar and update it with `$model->freshTimestampString()`.
**Action:** Always check for N+1 update scenarios in controllers when saving multiple records from request data. Apply chunked bulk updates using CASE statements, and make sure to respect `updated_at` if the model uses timestamps.
