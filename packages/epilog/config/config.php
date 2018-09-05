<?php
/*
 * Set specific configuration variables here
 */
return [
    'route'  => [
        'enabled'    => true,
        'middleware' => ['web', 'auth'],
        'prefix'     => 'cockpit',
    ],
    'view'   => [
        'layout' => 'ui::layouts.back',
    ],
    'menu'   => [
        'enabled' => true,
    ],
];
