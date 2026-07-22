## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2025-02-12 - Bulk Create Limitations When Returning Eloquent Objects
**Learning:** In Laravel, while you can easily resolve N+1 `firstOrNew` / `save` scenarios by pre-fetching existing records with a `whereIn` query, you cannot safely swap the creation of *new* records to a bulk `insert()` if the calling code expects Eloquent model events to fire or requires the auto-incremented primary keys (IDs) of the newly created items to be appended to a collection. Bulk inserts bypass events and don't natively return IDs.
**Action:** When refactoring to eliminate N+1 queries, verify whether the method relies on model events (like `deleting` or `created`) or needs primary keys. If so, limit your optimization to bulk-fetching the existing records and isolating the missing names for iteration. Iterate to create only the missing records one-by-one via Eloquent, retaining full compatibility.
