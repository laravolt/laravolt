## 2024-05-15 - Array check performance issue in ACL
**Learning:** Checking roles and permissions array sequentially via full evaluation, without exiting early, causes a noticeable O(n^2) scaling when checking lots of items.
**Action:** Always short circuit and return early in arrays evaluations and replace sequential array scans on collections with `.contains('key', 'val')` lookups.
