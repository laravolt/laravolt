<?php
/*
 * Set specific configuration variables here
 */
return [
    'route'            => [
        'enabled'    => true,
        'middleware' => ['web', 'auth'],
        'prefix'     => 'cms',
    ],
    'view'             => [
        'layout' => 'layouts.base',
    ],
    'menu'             => [
        'enabled' => true,
    ],
    'models'           => [
        'category' => \Laravolt\Comma\Models\Category::class,
        'post'     => \Laravolt\Comma\Models\Post::class,
        'tag'      => \Laravolt\Comma\Models\Tag::class,
    ],
    'default_title'    => 'Untitled',
    'default_type'     => 'post',
    'default_category' => 'Uncategorized',
];
