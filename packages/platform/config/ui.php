<?php

return [
    'brand_name' => env('APP_NAME', 'Laravolt'),
    'brand_image' => env('APP_URL').'/img/app.png',
    'font_size' => 'small',

    /*
     * Set default theme
     * Available themes: basik, black, blue, classic, fox, grey, pink, red, teal, violet
     * */
    'theme' => 'black',
    /*
     * Button color
     * Choose one of the following colors that match closely with the theme
     * Available colors: red, orange, yellow, olive, green, teal, blue, violet, purple, brow, grey, black
     * */
    'color' => 'blue',

    'animation' => env('APP_URL').'laravolt/lottiefiles/scan.json',
    'system_menu' => [
        'order' => 99,
    ],
    'quick_switcher' => false,
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
