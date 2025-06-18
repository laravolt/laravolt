<?php

return [
    'middleware' => ['web', 'auth'],
    'features' => [
        'database-monitor' => false,
        'epicentrum' => true,
        'mailkeeper' => false,
        'lookup' => false,
        'kitchen_sink' => false,
        'spa' => false,
        'quick_switcher' => false,
        'registration' => true,
        'verification' => true,
        'captcha' => false,
        'workflow' => false,
        'enable_default_menu' => true,
    ],
    'settings' => [
        [
            'type' => 'html',
            'content' => '<h3 class="ui horizontal divider section m-t-3">Informasi Umum</h3>',
        ],
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
            'type' => 'dropdown',
            'name' => 'font_size',
            'label' => 'Ukuran Font',
            'options' => ['sm' => 'Kecil', 'md' => 'Sedang', 'lg' => 'Besar'],
            'inline' => true,
        ],
        [
            'type' => 'dropdown',
            'name' => 'sidebar_density',
            'label' => 'Density',
            'options' => ['compact' => 'Compact', 'default' => 'Default'],
            'rules' => ['required'],
        ],
        [
            'type' => \Laravolt\Fields\Field::RADIO_GROUP,
            'name' => 'theme',
            'options' => ['dark' => 'Dark', 'light' => 'Light', 'cool' => 'Cool'],
            'label' => 'Tema',
            'inline' => true,
        ],
        [
            'type' => \Laravolt\Fields\Field::DROPDOWN_COLOR,
            'name' => 'color',
            'label' => 'Warna Aksen',
            'inline' => true,
        ],
        [
            'type' => 'html',
            'content' => '<h3 class="ui horizontal divider section m-t-3">Halaman Login</h3>',
        ],
        [
            'type' => 'dropdown',
            'options' => ['fullscreen' => 'Fullscreen', 'modern' => 'Modern', 'classic' => 'Classic'],
            'name' => 'login_layout',
            'label' => 'Layout',
            'inline' => true,
        ],
    ],
];
