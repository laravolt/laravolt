## 2024-05-14 - Collection overhead in query builder traits
**Learning:** Found an anti-pattern in `packages/suitable/src/AutoFilter.php` where `collect($castField)->filter(...)` was used simply to check if an array key exists and matches a value, which gets executed in a loop for every query filter. This introduces severe object allocation and closure execution overhead in what should be a fast query building path.
**Action:** Always prefer native array functions or direct hash map lookups (`isset($array[$key]) && $array[$key] === $value`) over Collection instances for simple array checks, especially inside loops or hot paths like Query Builder macros and traits.

## 2024-05-15 - Array check performance issue in ACL
**Learning:** Checking roles and permissions array sequentially via full evaluation, without exiting early, causes a noticeable O(n^2) scaling when checking lots of items.
**Action:** Always short circuit and return early in arrays evaluations and replace sequential array scans on collections with `.contains('key', 'val')` lookups.

## 2024-05-06 - Optimized _hasPermission N+1 Bottleneck
**Learning:** Checking permissions using the DB directly inside an Eloquent relation method (`_hasPermission`) causes N+1 problems in Laravolt Platform. Because Laravolt preloads `$this->permissions` via `protected $with = ['permissions'];`, performing a new `$permissionModel->where(...)->first()` query bypasses the loaded collection entirely. Eloquent Collection's `contains()` method handles string properties, integers, and Model instances effectively.
**Action:** Always prefer Eloquent Collection methods like `contains('name', $value)` or `contains('id', $value)` to check existence in pre-loaded many-to-many relationships over executing new database queries.
