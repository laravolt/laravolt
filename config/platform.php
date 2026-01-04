<?php

declare(strict_types=1);

return [
    'middleware' => ['web', 'auth'],
    'features' => [
        'database-monitor' => false,
        'epicentrum' => true,
        'mailkeeper' => false,
        // 'lookup' => false,
        // 'kitchen_sink' => false,
        // 'spa' => false,
        // 'quick_switcher' => false,
        'registration' => true,
        'verification' => true,
        // 'captcha' => false,
        // 'workflow' => false,
        'enable_default_menu' => true,
    ],
    'settings' => [
        [
            'type' => 'text',
            'name' => 'brand_name',
            'label' => 'Name',
        ],
        [
            'type' => 'text',
            'name' => 'brand_description',
            'label' => 'Description',
        ],
        [
            'type' => 'uploader',
            'name' => 'brand_image',
            'label' => 'Logo',
        ],
        [
            'type' => 'html',
            'content' => '<h3 class="ui horizontal divider hidden m-t-3">Tampilan Sidebar</h3>',
        ],
        [
            'type' => Laravolt\Fields\Field::DROPDOWN_COLOR,
            'name' => 'color',
            'label' => 'Warna Aksen',
            'inline' => true,
        ],
    ],
];
