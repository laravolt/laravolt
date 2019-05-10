<?php
/*
 * Set specific configuration variables here
 */
return [
    'columns' => [
        'except' => ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token']
    ],
    'view' => [
        'extends' => 'layout'
    ],
    'routes'     => [
        'prefix'    => '',
        'middleware' => [],
    ],
    'columns'    => [
        'except' => ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'],
    ],
    'namespace'  => 'Modules',
    'target_dir' => base_path('modules')
];
