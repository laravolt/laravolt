<?php

/*
 * Set specific configuration variables here
 */
return [
    'routes' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'workflow',
    ],
    'menu' => [
        'enabled' => true,
    ],
];
