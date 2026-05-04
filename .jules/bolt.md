## 2024-05-18 - Eloquent Timestamps in Bulk Updates
**Learning:** In Laravolt, when replacing O(N) model loops with O(1) bulk `increment`/`decrement` queries on an Eloquent Builder, it automatically updates `updated_at` timestamps, unlike individual `$model->timestamps = false` loops.
**Action:** Use `->toBase()` before calling `increment()` or `decrement()` on an Eloquent Builder to execute the query against the underlying base Query Builder, which safely bypasses automatic timestamp updates and preserves original exact behavior.
