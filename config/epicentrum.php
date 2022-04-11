<?php

/*
 * Set specific configuration variables here
 */
return [
    'role' => [
        'multiple' => true,
        'editable' => true,
    ],
    'repository' => [
        'user' => \Laravolt\Epicentrum\Repositories\EloquentRepository::class,
        'role' => \Laravolt\Epicentrum\Repositories\RoleRepository::class,
        'timezone' => \Laravolt\Support\Repositories\TimezoneRepository::class,
        'searchable' => ['name', 'email', 'status'],
    ],
    'requests' => [
        'account' => [
            'store' => \Laravolt\Epicentrum\Http\Requests\Account\Store::class,
            'update' => \Laravolt\Epicentrum\Http\Requests\Account\Update::class,
            'delete' => \Laravolt\Epicentrum\Http\Requests\Account\Delete::class,
        ],
    ],
    // Max user allowed
    // null or 0 mean unlimited
    'user_limit' => null,
    'user_available_status' => [
        'PENDING' => 'PENDING',
        'ACTIVE' => 'ACTIVE',
        'BLOCKED' => 'BLOCKED',
    ],
    'models' => [
        'user' => \App\Models\User::class,
        'role' => \Laravolt\Platform\Models\Role::class,
        'permission' => \Laravolt\Platform\Models\Permission::class,
    ],
    'table_view' => \Laravolt\Epicentrum\Livewire\UserTable::class,
];
