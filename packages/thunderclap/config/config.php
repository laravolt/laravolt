<?php
/*
 * Set specific configuration variables here
 */
return [
    'routes'     => [
        'prefix'    => '',
        'middleware' => [],
    ],
    'columns'    => [
        'except' => ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'],
    ],
    'namespace'  => 'App',
    'target_dir' => app_path()
];
