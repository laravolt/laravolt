## 2024-05-14 - Collection overhead in query builder traits
**Learning:** Found an anti-pattern in `packages/suitable/src/AutoFilter.php` where `collect($castField)->filter(...)` was used simply to check if an array key exists and matches a value, which gets executed in a loop for every query filter. This introduces severe object allocation and closure execution overhead in what should be a fast query building path.
**Action:** Always prefer native array functions or direct hash map lookups (`isset($array[$key]) && $array[$key] === $value`) over Collection instances for simple array checks, especially inside loops or hot paths like Query Builder macros and traits.

## 2024-05-15 - Array check performance issue in ACL
**Learning:** Checking roles and permissions array sequentially via full evaluation, without exiting early, causes a noticeable O(n^2) scaling when checking lots of items.
**Action:** Always short circuit and return early in arrays evaluations and replace sequential array scans on collections with `.contains('key', 'val')` lookups.

## 2024-05-24 - Avoid N+1 in Role::hasPermission
**Learning:** Checking permissions on `Role` using `hasPermission()` was previously doing an unnecessary database query (`first()`) for every check instead of using the already loaded `permissions` relationship. This resulted in an N+1 queries issue, significantly impacting performance when dealing with many permissions checks.
**Action:** For simple existence checks within an Eloquent model, use the Eloquent Collection's `contains` method on the loaded relationship instead of querying the database directly. This leverages the in-memory array representation which is substantially faster.
