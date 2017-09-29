<?php

/*
 * Set specific configuration variables here
 */
return [
    'script_placeholder' => 'script.foot',
    'query_string'       => [
        'sort_by'        => 'sort',
        'sort_direction' => 'direction',
        'search'         => 'search',
    ],
    'pagination_view'    => 'suitable::pagination.full',
    'restful_button'     => [
        'delete_confirmation'        => 'Are you sure you want to delete this item?',
        'delete_confirmation_auto'   => 'Are you sure you want to delete :item?',
        'delete_confirmation_fields' => ['title', 'name'],
    ],
];
