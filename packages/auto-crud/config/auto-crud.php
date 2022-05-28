<?php

return [
    'routes' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'resource',
    ],
    'menu' => [
        'enabled' => true,
        'label' => 'Resources',
    ],
    'permission' => \Laravolt\Platform\Enums\Permission::MANAGE_SYSTEM,
];
