<?php

return [
    'route' => [
        'enable' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => '',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enable' => true,
    ],
    'permission' => [],
    'collections' => [
        // Sample lookup collections
        // 'pekerjaan' => [
        //     'label' => 'Pekerjaan',
        // ],
    ],
];
