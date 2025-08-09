<?php

return [
    // When true, sends the header as Content-Security-Policy-Report-Only
    // Defaults to true to avoid breaking pages until you harden and add nonces where needed
    'report_only' => env('CSP_REPORT_ONLY', true),

    // You can use the {nonce} placeholder which will be replaced per-request
    'except' => explode(',', (string) env('CSP_EXCEPT', '')),

    'directives' => [
        // Examples of sane defaults. Adjust as needed for your app/assets/CDNs.
        'default-src' => ["'self'"],
        'base-uri' => ["'self'"],
        'frame-ancestors' => ["'self'"],
        'object-src' => ["'none'"],
        'form-action' => ["'self'"],

        // Scripts and styles allow nonce; styles often need 'unsafe-inline' due to 3rd-party libs
        'script-src' => ["'self'", "'nonce-{nonce}'", 'https:'],
        'style-src' => ["'self'", "'nonce-{nonce}'", 'https:', "'unsafe-inline'"],

        // Adjust connect/img/font/frame-src for your needs
        'connect-src' => ["'self'", 'https:'],
        'img-src' => ["'self'", 'data:', 'blob:', 'https:'],
        'font-src' => ["'self'", 'data:', 'https:'],
        'frame-src' => ["'self'", 'https:'],

        // 'report-to' or 'report-uri' can be added if you have reporting endpoints
        // 'report-uri' => [url('/csp-report')],

        // Optional global switches
        // 'upgrade-insecure-requests' => [],
        // 'block-all-mixed-content' => [],
    ],
];