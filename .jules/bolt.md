## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-07-07 - Bulk UPDATE optimization for PermissionController
**Learning:** In standard CRUD controllers that update multiple items submitted via a form (like editing permissions for all roles at once), looping through requests and calling `update()` leads to an N+1 query problem on the backend.
**Action:** Replace `foreach` loops containing `update()` with a single `UPDATE` query utilizing a `CASE` statement. Ensure to properly fetch the table name and column names wrapped by the query grammar, and manually update the `updated_at` timestamp if the model uses timestamps, being mindful that a model might disable `updated_at` by setting the constant to null.
