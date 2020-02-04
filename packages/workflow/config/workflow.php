<?php

return [
    'route' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'workflow',
    ],
    'menu' => [
        'enabled' => true,
    ],
    'view' => [
        'layout' => 'laravolt::layouts.app',
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
    'business_key' => 'bkey',
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

    'tables' => [

        /*
        |--------------------------------------------------------------------------
        | Tabel Infrastruktur
        |--------------------------------------------------------------------------
        |
        | Adalah tabel yang isinya menentukan behaviour aplikasi.
        | Format: <table_name> => <order_by_column>.
        | <order_by_column> diperlukan untuk chunking query agar tidak out of memory
        |
        */
        'infrastructure' => [
            'workflow_module' => 'id',
            'acl_permissions' => 'id',
            'workflow_permission' => 'module_id',
            'menu' => 'id',
            'surat' => 'id',
            'camunda_form' => 'id',
            'segments' => 'id',
            'lookup' => 'id',
        ],

        /*
        |--------------------------------------------------------------------------
        | Tabel Transaksi
        |--------------------------------------------------------------------------
        |
        | Adalah tabel transaksional yang isinya merupakan hasil dari aktivitas pengguna.
        |
        |
        */
        'transaction' => [
            // Semua tabel yang digenerate dari BPMN tidak perlu disebutkan disini.
            // Cukup daftarkan tabel baru yang biasanya digunakan untuk menyimpan relasi has many.
            'camunda_task',
            'parameter_test',
            'penerimaan_sampel_multirow',
        ],
    ],

];
