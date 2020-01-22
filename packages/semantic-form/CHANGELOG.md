# Change Log
All notable changes to semantic-form will be documented in this file.
## 2.4.1 (2019-09-16)
* Add new component `rupiah()`

## 2.0.0 (2019-02-06)
* Minimum PHP version changed to 7.1.3
* Minimum Laravel version changed to 5.7
* Add horizontal form
* Add `Macroable`
* Add global helper `form()`
* Alias `SemanticForm` are now deprecated, use alias `Form::text()` or helper `form()->text()`
* Add new component `link()`
* Add new component `action()`
* Remove undocumented function `setToken()`
* Automatically generate CSRF token when opening form
* Add chainable method `withoutToken()`  when opening form
* Add optional second param to `open($route, $bind)` to bind model
* Make `bind()` chainable
* Add optional second param to `label($text, $fieldCallback)` to modify Field wrapper
* Component `submit()` will have class `ui button primary` as default
