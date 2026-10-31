<?php

declare(strict_types=1);

/*
 * The built-in "System" menu. Once published to config/laravolt/menu/system.php,
 * your copy replaces this file entirely: entries you remove disappear from the
 * menu. To add your own sections, create another file in config/laravolt/menu/
 * instead. Set laravolt.platform.features.enable_default_menu to false to drop
 * the System menu altogether.
 */

return [
    'System' => [
        'order' => 99,
        'menu' => [
            'Users' => [
                'route' => 'epicentrum::users.index',
                'active' => 'epicentrum/users/*',
                'icon' => 'user-friends',
                'permissions' => [Laravolt\Platform\Enums\Permission::MANAGE_USER],
            ],
            'Roles' => [
                'route' => 'epicentrum::roles.index',
                'active' => 'epicentrum/roles/*',
                'icon' => 'user-astronaut',
                'permissions' => [Laravolt\Platform\Enums\Permission::MANAGE_ROLE],
            ],
            'Permissions' => [
                'route' => 'epicentrum::permissions.edit',
                'active' => 'epicentrum/permissions/*',
                'icon' => 'shield-check',
                'permissions' => [Laravolt\Platform\Enums\Permission::MANAGE_PERMISSION],
            ],
            'Settings' => [
                'route' => 'platform::settings.edit',
                'active' => 'platform/settings/*',
                'icon' => 'sliders-v',
                'permissions' => [Laravolt\Platform\Enums\Permission::MANAGE_SETTINGS],
            ],
        ],
    ],
];
