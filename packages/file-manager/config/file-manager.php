<?php

/*
 * Set specific configuration variables here
 */
return [
    'routes' => [
        'middleware' => ['web', 'auth'],
        'prefix' => 'file-manager',
    ],
    'query_string' => 'key',
];
