<?php

return [
    'routes' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'workflow',
    ],
    'view' => [
        'layout' => 'ui::layouts.app',
    ],
    'form' => [
        // 'class' => 'horizontal',
    ],
    'process_instance' => [
        'editable' => false,
    ],
    'authorization' => [
        'enabled' => true,
    ],
    'business_key' => 'no_agenda',
    /*
    |--------------------------------------------------------------------------
    | Strict mode
    |--------------------------------------------------------------------------
    |
    | Jika false, maka setiap field yang dikirim dari form TETAPI TIDAK ADA di database akan DI-IGNORE tanpa raise Exception
    | Jika true, akan muncul Exception COLUMN NOT FOUND.
    |
    */
    'strict' => true,
    /*
    |--------------------------------------------------------------------------
    | Auto Save
    |--------------------------------------------------------------------------
    |
    | Jika diisi angka x, dimana x > 0, maka form input task akan otomatis di-save sebagai DRAFT setiap x milisecond.
    | Jika diisi 0 atau false, maka auto save tidak akan dijalankan
    |
    */
    'auto_save' => 10000, // in miliseconds
];
