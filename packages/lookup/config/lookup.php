<?php

return [
    'route' => [
        'enable' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => '',
    ],
    'view' => [
        'layout' => 'ui::layouts.app',
    ],
    'menu' => [
        'enable' => true,
    ],
    'permission' => null,
    'collections' => [
        // Sample lookup collections
        // 'pekerjaan' => [
        //     'label' => 'Pekerjaan',
        // ],
    ],
];
