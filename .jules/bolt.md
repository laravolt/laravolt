## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2025-05-19 - Bulk UPDATE Database Binding Limits
**Learning:** When executing raw SQL `UPDATE` statements using `CASE` clauses, each dynamic parameter (such as ID checks and new values) adds bindings. Some database drivers (e.g., SQLite, SQL Server) have strict limits on the number of bound parameters per query (e.g., 999). Without batching, updating a large number of rows simultaneously can crash the query.
**Action:** Always wrap large or unbounded raw SQL bulk operations in a chunking loop (e.g., `foreach (array_chunk($data, 300) as $chunk)`) to ensure parameter limits are not exceeded, balancing performance with safety.
