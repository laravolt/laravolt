<?php

return [
    'features' => [
        'epilog' => false,
        'database-monitor' => false,
        'mailkeeper' => false,
        'lookup' => false,
        'kitchen_sink' => false,
        'turbolinks' => false,
        'quick_switcher' => true,
        'registration' => true,
        'verification' => true,
        'captcha' => false,
        'workflow' => true,
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
            'type' => 'html',
            'content' => '<div class="ui divider section"></div>',
        ],
        [
            'type' => 'dropdown',
            'name' => 'font_size',
            'label' => 'Ukuran Font',
            'options' => ['xs' => 'Paling Kecil', 'sm' => 'Kecil', 'md' => 'Sedang', 'lg' => 'Besar', 'xl' => 'Paling Besar'],
            'inline' => true,
        ],
        [
            'type' => 'dropdown',
            'name' => 'theme',
            'options' => ['dark' => 'Gelap', 'light' => 'Terang'],
            'label' => 'Tema Sidebar',
            'inline' => true,
        ],
        [
            'type' => 'dropdownColor',
            'name' => 'color',
            'label' => 'Warna Aksen',
            'inline' => true,
        ],
        [
            'type' => 'html',
            'content' => '<div class="ui divider section"></div>',
        ],
        [
            'type' => 'dropdown',
            'options' => ['fullscreen' => 'Fullscreen', 'modern' => 'Modern', 'classic' => 'Classic'],
            'name' => 'login_layout',
            'label' => 'Layout Halaman Login',
            'inline' => true,
        ],
    ],
];
