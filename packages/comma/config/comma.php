<?php

/*
 * Set specific configuration variables here
 */
return [
    'route' => [
        'enabled' => false,
        'middleware' => ['web', 'auth'],
        'prefix' => 'cms',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
    ],
    'menu' => [
        'enabled' => false,
    ],
    'models' => [
        'post' => \Laravolt\Comma\Models\Post::class,
        'tag' => \Laravolt\Comma\Models\Tag::class,
    ],
    'default_type' => 'post',
    'collections' => [
        'default' => [
            'label' => 'All Posts',
            'data' => [
                'icon' => 'newspaper outline',
                // 'permission' => 'manage-all-posts',
            ],
            'filters' => [
                'type' => 'default',
            ],
        ],
    ],
];
