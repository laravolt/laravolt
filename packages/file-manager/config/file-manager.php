<?php

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
