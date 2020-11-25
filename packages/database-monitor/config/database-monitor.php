<?php

/*
 * Set specific configuration variables here
 */
return [
    'route' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'database-monitor',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enabled' => true,
    ],
    'disk' => env('DB_BACKUP_DISK', 'local-backup')
];
