<?php

return [
    'route' => [
        'enable' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'menu-manager',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enable' => true,
    ],
];
