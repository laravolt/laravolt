<?php
/*
 * Set specific configuration variables here
 */
return [
    'route' => [
        'enabled'    => true,
        'middleware' => ['web', 'auth'],
        'prefix'     => 'cms',
    ],
    'view'  => [
        'layout' => 'layouts.base',
    ],
    'menu'  => [
        'enabled' => true,
    ],
];
