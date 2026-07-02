## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-12 - Optimized Permission Description Mass Update
**Learning:** Laravolt's `PermissionController@update` historically processed description updates inside a `foreach` loop, executing a single SQL `UPDATE` statement per role. While Laravel lacks a native bulk update method without inserting (like `upsert`), raw SQL `CASE ... WHEN` statements can achieve bulk updates safely if PDO parameterized bindings are used and chunks are applied to avoid parameter limits (e.g. SQLite's 999 limit).
**Action:** Replaced the loop with a single chunked raw `UPDATE` using `CASE`. Used Eloquent's Query Grammar (`$grammar->wrapTable()`) to securely wrap table and column identifiers and properly respect multi-database or prefixed environments, while maintaining the same event-bypassing timestamp behavior as `whereId()->update()`.
