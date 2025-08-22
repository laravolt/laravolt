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
        'async' => [
            'enabled' => env('SHAR_ASYNC_ENABLED', true),
            'default_queue' => env('SHAR_DEFAULT_QUEUE', 'shar-workflows'),
            'queues' => [
                'workflows' => env('SHAR_WORKFLOW_QUEUE', 'shar-workflows'),
                'instances' => env('SHAR_INSTANCE_QUEUE', 'shar-instances'),
                'sync' => env('SHAR_SYNC_QUEUE', 'shar-sync'),
            ],
            'sync_interval' => env('SHAR_SYNC_INTERVAL', 300), // 5 minutes
            'notifications' => env('SHAR_NOTIFICATIONS_ENABLED', true),
        ],
    ],
];
