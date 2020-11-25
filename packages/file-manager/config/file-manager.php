<?php

/*
 * Set specific configuration variables here
 */
return [
    'route' => [
        'middleware' => ['web', 'auth'],
        'prefix' => 'file-manager',
    ],
    'query_string' => 'key',
];
