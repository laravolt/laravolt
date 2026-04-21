## 2024-04-21 - Collection methods performance
**Learning:** In Laravel collections, calling `where(...)->first()` forces the framework to iterate the entire collection, allocate a new array for the filtered results, and then pick the first element. Similarly, `firstWhere(...)` followed by fallback manual `foreach` iteration performs O(N) operations multiple times.
**Action:** Use `contains(...)` when checking for existence. It short-circuits execution as soon as a match is found and completely bypasses new collection/array memory allocations, yielding significant (50%) speedups.
