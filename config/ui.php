<?php

return [
    'brand_name' => env('APP_NAME', 'Laravolt'),
    'brand_image' => null,
    'brand_description' => env('APP_DESCRIPTION', 'Sample application powered by Laravolt'),

    /*
     * Font Size
     * Available options: xs, sm, md, xl, lg
     * */
    'font_size' => 'sm',

    /*
     * Set default theme
     * Available themes: dark, light
     * */
    'theme' => 'light',

    /*
     * Button color
     * Choose one of the following colors that match closely with the theme
     * Available colors: red, orange, yellow, olive, green, teal, blue, violet, purple, brow, grey, black
     * */
    'color' => 'teal',

    /*
     * Login page settings
     */
    // Layout, Available options: 'modern', 'fullscreen', 'classic'
    'login_layout' => 'modern',

    // Background image
    'login_background' => '/laravolt/img/wallpaper/animated-svg/dark.svg',

    /*
     * Iconset
     * Available options: fad (two tone), fal (light), far (regular)
     * Browse icons at https://fontawesome.com/icons?d=gallery&p=2&s=duotone&m=pro
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
        'red' => '#DB2828',
        'orange' => '#F2711C',
        'yellow' => '#FBBD08',
        'olive' => '#B5CC18',
        'green' => '#21BA45',
        'teal' => '#00B5AD',
        'blue' => '#0052CC',
        'violet' => '#6435C9',
        'purple' => '#A333C8',
        'pink' => '#E03997',
        'brown' => '#A5673F',
        'grey' => '#767676',
        'black' => '#1B1C1D',
    ],
];
