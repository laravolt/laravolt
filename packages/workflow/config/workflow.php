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
];
