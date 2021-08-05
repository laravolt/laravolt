<?php

return [
    'routes' => [
        'enabled' => true,
        'middleware' => config('laravolt.platform.middleware'),
        'prefix' => '',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enabled' => true,
    ],
    'permission' => \Laravolt\Platform\Enums\Permission::MANAGE_LOOKUP,
    'collections' => [
        // Sample lookup collections
        'pekerjaan' => [
            'label' => 'Pekerjaan',
        ],
    ],
];
