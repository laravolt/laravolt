<?php

/*
 * Set specific configuration variables here
 */
return [
    'routes' => [
        'enabled' => true,
        'middleware' => config('laravolt.platform.middleware'),
        'prefix' => 'workflow',
    ],
    'menu' => [
        'enabled' => true,
    ],
    'shar' => [
        'base_url' => env('SHAR_BASE_URL', 'http://localhost:8080'),
        'timeout' => env('SHAR_TIMEOUT', 30),
        'enabled' => env('SHAR_ENABLED', false),
        'nats_url' => env('NATS_URL', 'nats://127.0.0.1:4222'),
        'log_level' => env('SHAR_LOG_LEVEL', 'info'),
    ],
];
