<?php
/*
 * Set specific configuration variables here
 */
return [
    'route'  => [
        'enabled'    => true,
        'middleware' => ['web', 'auth'],
        'prefix'     => 'epilog',
    ],
    'view'   => [
        'layout' => 'ui::layouts.back',
    ],
    'menu'   => [
        'enabled' => true,
    ],
];
