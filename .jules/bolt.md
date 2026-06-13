## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2025-06-13 - Batched Database Session Deletion
**Learning:** In the `AccessControlInvalidator` class, passing an iterable to `invalidateUsers` triggered a loop of queries to check schema structure and execute `DELETE` statements on the database sessions table for each user individually.
**Action:** Always batch deletes for related models/entities where possible. In this instance, extracting the user IDs and using `whereIn('user_id', $userIds)->delete()` along with running `Arr::wrap()` inside the underlying protection function completely mitigates the N+1 problem without disrupting single ID deletion paths.
