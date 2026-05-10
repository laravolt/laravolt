## 2024-05-14 - Collection overhead in query builder traits
**Learning:** Found an anti-pattern in `packages/suitable/src/AutoFilter.php` where `collect($castField)->filter(...)` was used simply to check if an array key exists and matches a value, which gets executed in a loop for every query filter. This introduces severe object allocation and closure execution overhead in what should be a fast query building path.
**Action:** Always prefer native array functions or direct hash map lookups (`isset($array[$key]) && $array[$key] === $value`) over Collection instances for simple array checks, especially inside loops or hot paths like Query Builder macros and traits.

## 2024-05-15 - Array check performance issue in ACL
**Learning:** Checking roles and permissions array sequentially via full evaluation, without exiting early, causes a noticeable O(n^2) scaling when checking lots of items.
**Action:** Always short circuit and return early in arrays evaluations and replace sequential array scans on collections with `.contains('key', 'val')` lookups.

## 2024-05-16 - N+1 Queries in Permission Checks
**Learning:** Found a severe performance bottleneck where checking permissions via the `Role::_hasPermission` method resulted in an O(N) database query pattern (executing `find` or `where()->first()` for every single permission check), despite the underlying relationship being eager-loaded (`protected $with = ['permissions']`).
**Action:** Always prefer utilizing eager-loaded Eloquent Collections in-memory (`$this->permissions->contains()`) for existence checks rather than querying the database again. To preserve dynamic primary key type checking while skipping the database, use collection callbacks (e.g., `contains(fn($model) => $model->getKey() === $value)`).
