## 2024-05-14 - Role sync optimization
**Learning:** `resolveRoleIds` iterates over strings and creates missing roles via `firstOrCreate` one by one, generating N+1 queries.
**Action:** Use the same optimization logic applied to `Role::syncPermission` (batch-query missing roles and then create them) to prevent N+1 issues when parsing strings.
