## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.
## 2024-05-11 - Optimized Permission Check
**Learning:** Laravolt's `HasRoleAndPermission` and `Role` models do dynamic query checks or `contains` lookups when `hasPermission` is called. Since permissions are eager-loaded, doing a `contains` operation manually can bypass `app(config(...))` overhead and Model instantiation.
**Action:** Overrode `_hasPermission` in models to utilize eager-loaded relations properly.

## 2024-05-15 - Fixed fatal error when using facade vs global function
**Learning:** `str()` is a global helper in Laravel and doesn't require an import, but `Str::isUuid()` requires the `Illuminate\Support\Str` facade to be imported. The original code used the global helper, and during optimization I mistakenly used the Facade without importing it.
**Action:** When refactoring existing string manipulation logic, stick to the original style (either global helper or Facade) to avoid missing import errors.
