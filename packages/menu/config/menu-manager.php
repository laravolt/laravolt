<?php

return [
    'route' => [
        'enable' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'menu-manager',
    ],
    'view' => [
        'layout' => 'ui::layouts.app',
    ],
    'menu' => [
        'enable' => true,
    ],
];
