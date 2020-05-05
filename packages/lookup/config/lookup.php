<?php

return [
    'route' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => '',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enabled' => true,
    ],
    'permission' => [\Laravolt\Platform\Enums\Permission::MANAGE_LOOKUP],
    'collections' => [
        // Sample lookup collections
        'pekerjaan' => [
            'label' => 'Pekerjaan',
        ],
    ],
];
