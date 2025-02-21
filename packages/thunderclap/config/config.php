<?php

/*
 * Set specific configuration variables here
 */
return [
    'columns' => [
        'except' => ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'],
    ],
    'view' => [
        'extends' => 'layout',
    ],
    'routes' => [
        'prefix' => '',
        'middleware' => [],
    ],
    'namespace' => 'Modules',
    'target_dir' => base_path('modules'),
    'transformer' => \Laravolt\Thunderclap\LaravoltTransformer::class,
    'prefixed' => [
        'ServiceProvider.php',
        'Controller.php',
        'TableView.php',
        'Resource.php',
    ],

    // Template skeleton (stubs)
    'default' => 'laravolt',

    // name => directory path, relative with stubs directory or absolute path
    'templates' => [
        'laravolt' => 'laravolt',
    ],
];
