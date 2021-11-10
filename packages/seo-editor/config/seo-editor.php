<?php

/*
 * Set specific configuration variables here
 */
return [
    'routes' => [
        'enabled' => true,
        'middleware' => config('laravolt.platform.middleware'),
        'prefix' => 'seo-editor',
    ],
    'menu' => [
        'enabled' => true,
    ],
];
