<?php

/*
 * Set specific configuration variables here
 */
return [
    'query_string'   => [
        'sort_by'        => 'sort',
        'sort_direction' => 'direction',
        'search'         => 'search',
    ],
    'restful_button' => [
        'delete_confirmation_fields' => ['title', 'name'],
    ],
];
