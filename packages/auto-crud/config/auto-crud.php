<?php

return [
    'routes' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'resource',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enabled' => true,
        'label' => 'Resources',
    ],
    'permission' => \Laravolt\Platform\Enums\Permission::MANAGE_SYSTEM,
    'resources' => [
        // Sample resources
        // 'post' => [
        //     'label' => 'Post',
        //     'model' => \App\Models\Post::class,
        //     'schema' => [
        //         [
        //             'type' => 'text',
        //             'name' => 'title',
        //             'label' => 'Title',
        //         ],
        //         [
        //             'type' => 'redactor',
        //             'name' => 'content',
        //             'label' => 'Content',
        //         ],
        //     ],
        // ],
    ],
];
