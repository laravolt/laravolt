<?php

return [
    'middleware' => ['web', 'auth'],
    'features' => [
        'epilog' => false,
        'database-monitor' => false,
        'epicentrum' => true,
        'mailkeeper' => false,
        'lookup' => true,
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
            // 'rules' => ['required'],
        ],
        [
            'type' => 'html',
            'content' => '<h3 class="ui horizontal divider hidden m-t-3">Tampilan Sidebar</h3>',
        ],
        [
            'type' => 'dropdown',
            'name' => 'font_size',
            'label' => 'Ukuran Font',
            'options' => ['xs' => 'Paling Kecil', 'sm' => 'Kecil', 'md' => 'Sedang', 'lg' => 'Besar', 'xl' => 'Paling Besar'],
            'inline' => true,
            'rules' => ['required'],
        ],
        [
            'type' => 'dropdown',
            'name' => 'theme',
            'options' => ['dark' => 'Gelap', 'light' => 'Terang'],
            'label' => 'Tema',
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
