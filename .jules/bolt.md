## 2024-07-16 - Batch Schema Checks in Bulk Operations
**Learning:** When performing bulk invalidation or cleanup (like `AccessControlInvalidator::deleteDatabaseSessions`), placing schema checks (`Schema::hasTable` and `Schema::hasColumn`) inside the loop creates a silent but significant N+1 query issue, as they trigger separate information schema queries for each iteration.
**Action:** Extract user IDs into an array, run the schema check exactly once, and use `whereIn()->delete()` to process the entire batch in a single database round-trip.

## 2024-07-16 - Exception Catching
**Learning:** `Intervention\Image\Exceptions\InvalidArgumentException` in v4.x does not inherit from PHP's built-in `\InvalidArgumentException`.
**Action:** When catching this specific exception, explicitly import or use its FQCN `\Intervention\Image\Exceptions\InvalidArgumentException` in the `catch` block to ensure it is properly handled.
