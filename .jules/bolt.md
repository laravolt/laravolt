
## 2024-05-18 - Replacing Unnecessary Lookup/Loop Pairs with Contains
**Learning:** Found an anti-pattern in `hasRole` where `firstWhere` was used to fetch an object from a collection, and then a manual foreach loop was used on the same collection to check if the role was present using `$role->is($assignedRole)`. This is a redundant O(2n) check allocating an unnecessary object.
**Action:** Use `$collection->contains()` directly instead, which operates in O(n) or better, avoids object allocations, and automatically falls back to `is()` under the hood if passed a Model.
