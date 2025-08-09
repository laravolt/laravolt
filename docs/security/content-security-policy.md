### Content Security Policy (CSP)

A Content Security Policy helps prevent XSS and data injection attacks by whitelisting trusted sources for scripts, styles, images, and more. This package ships with a configurable CSP middleware that supports nonces for inline scripts/styles.

#### What it does
- Adds a per-request nonce and exposes it to Blade as `csp_nonce()` (and `$cspNonce`).
- Compiles a CSP header from `config/csp.php` with `{nonce}` replacements.
- Sends either `Content-Security-Policy` (enforced) or `Content-Security-Policy-Report-Only` (report-only).
- Can be skipped for specific routes via patterns.

#### Where it runs
The middleware is registered in `config/platform.php` under the `middleware` stack used by all `platform` routes. You can move it to other stacks as needed.

#### Quick start
1) Keep report-only mode while you iterate (default):
```
CSP_REPORT_ONLY=true
```
2) Visit your pages and review violations in the browser DevTools console or the Network panel.
3) Update `config/csp.php` to allow required sources. Use nonces for any inline `<script>`/`<style>`.
4) When satisfied, enforce by disabling report-only:
```
CSP_REPORT_ONLY=false
```

#### Using the nonce in Blade
Add the nonce to any inline scripts or styles so they pass CSP:
```blade
<script nonce="{{ csp_nonce() }}">
    window.App = { locale: '{{ app()->getLocale() }}' };
</script>

<style nonce="{{ csp_nonce() }}">
    .hidden { display: none; }
</style>
```

You can also access `$cspNonce` if you prefer:
```blade
<script nonce="{{ $cspNonce }}">/* ... */</script>
```

External scripts loaded via `<script src="...">` do not need a nonce, but their origin must be allowed in `script-src`.

#### Configuration (`config/csp.php`)
Key options:
- `report_only` (bool): When true, sends `Content-Security-Policy-Report-Only`.
- `except` (array or CSV via `CSP_EXCEPT`): Route patterns to skip CSP (e.g., `telescope*`, `debugbar*`).
- `directives` (array): Map of CSP directives. Supports `{nonce}` placeholder which is replaced per-request.

Example snippet:
```php
return [
    'report_only' => env('CSP_REPORT_ONLY', true),
    'except' => explode(',', (string) env('CSP_EXCEPT', 'telescope*,debugbar*')),
    'directives' => [
        'default-src' => ["'self'"],
        'script-src'  => ["'self'", "'nonce-{nonce}'", 'https://www.googletagmanager.com'],
        'style-src'   => ["'self'", "'nonce-{nonce}'", 'https:', "'unsafe-inline'"],
        'img-src'     => ["'self'", 'data:', 'blob:', 'https:'],
        'connect-src' => ["'self'", 'https:'],
        'font-src'    => ["'self'", 'data:', 'https:'],
        'frame-src'   => ["'self'", 'https:'],
        // 'upgrade-insecure-requests' => [],
        // 'report-uri' => [url('/csp-report')],
    ],
];
```

#### Allowing third-party providers
- Analytics (e.g., Google Tag Manager): add their domains to `script-src` and `img-src` if beacons are used.
- Fonts/CDNs: add to `font-src`, `style-src`, and `img-src` as appropriate.
- iframes/widgets: add provider to `frame-src` (and possibly `child-src` for older browsers).

#### Route-level skipping
To disable CSP on specific endpoints, set patterns via env:
```
CSP_EXCEPT=telescope*,debugbar*,web-tinker*
```
You can also remove the middleware from specific route groups and apply it only where desired.

#### Reporting
You can enable reporting to collect violations safely in production without enforcing:
- Set `CSP_REPORT_ONLY=true` and add a `report-uri` (or `report-to`) directive pointing to a logging endpoint.
- Ensure the endpoint accepts JSON reports. Example directive:
```php
'report-uri' => [url('/csp-report')],
```

#### Migration to enforcement
- Keep report-only until the console is clean.
- Switch `CSP_REPORT_ONLY=false` to enforce.
- Monitor error logs and adjust `directives` if new features are added.

#### Tips
- Prefer nonces over `'unsafe-inline'` for scripts. Keep `'unsafe-inline'` only if necessary for styles.
- Avoid wildcards like `*`. Prefer protocol/host-specific entries (e.g., `https://cdn.example.com`).
- Regenerate nonces per request (handled automatically by the middleware).