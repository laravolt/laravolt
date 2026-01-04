<?php

declare(strict_types=1);

/*
 * Set specific configuration variables here
 */
return [
    'routes' => [
        'middleware' => config('laravolt.platform.middleware'),
        'prefix' => 'file-manager',
    ],
    'query_string' => 'key',
];
