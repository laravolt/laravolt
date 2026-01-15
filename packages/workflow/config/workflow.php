<?php

declare(strict_types=1);

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
