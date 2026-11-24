<?php

declare(strict_types=1);

return [
    'brand_name' => env('APP_NAME', 'Laravolt'),
    'brand_image' => '/laravolt/assets/images/logo.svg',
    // 'brand_image' => 'https://ub.test/laravolt/assets/images/logo.svg',
    'brand_description' => env('APP_DESCRIPTION', 'Sample application powered by Laravolt'),

    /*
     * Theme
     * null keeps the classic look (gray and blue). 'workbench' is Laravolt's own
     * theme: graphite surfaces and the brand indigo, in light and dark mode.
     * Any other name renders <html data-theme="theme-{name}"> for a Preline 5
     * theme in your own stylesheet.
     * */
    'theme' => env('LARAVOLT_UI_THEME'),

    /*
     * Button color (classic theme only; a theme sets its own accent)
     * Choose one of the following colors that match closely with the theme
     * Available colors: blue, indigo, cyan, teal
     * */
    'color' => 'blue',

    // Background image
    'login_background' => '/laravolt/img/wallpaper/animated-svg/dark.svg',

    /*
     * Iconset
     * Available options: fad (two tone), fal (light), far (regular), fas (solid)
     * Browse icons at https://fontawesome.com/v5/search
     */
    'iconset' => 'fad',

    'default_menu_icon' => 'circle',

    'system_menu' => [
        'order' => 99,
    ],
    'flash' => [
        'attributes' => [
            'class' => 'black',
        ],
        'except' => [],
    ],
    'mail' => [
        'header' => '#345',
        'body' => '#F2F5F7',
        'content' => [
            'color' => '#2E3C4A',
            'background' => '#FFFFFF',
        ],
        'button' => [
            'color' => '#FFFFFF',
            'background' => '#0570D4',
        ],
    ],
    'colors' => [
        'blue' => '#2563EB',
        'indigo' => '#4F46E5',
        'cyan' => '#0891B2',
        'teal' => '#0D9488',
    ],
];
