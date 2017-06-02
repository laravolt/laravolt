<?php

return [
    'header'     => [
        'index'  => 'Category',
        'create' => 'Add Category',
    ],
    'action'     => [
        'create' => 'Add Category',
        'submit' => 'Save',
    ],
    'message'    => [
        'create_success'                 => 'New category added',
        'update_success'                 => 'Category updated',
        'delete_success'                 => 'Category deleted',
        'delete_failed'                  => 'Delete category failed',
        'cannot_delete_default_category' => 'Default category cannot be deleted',
    ],
    'attributes' => [
        'name' => 'Label',
        'slug' => 'Slug',
    ],
];
