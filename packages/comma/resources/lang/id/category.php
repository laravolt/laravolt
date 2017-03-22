<?php

return [
    'header'     => [
        'index'  => 'Kategori',
        'create' => 'Tambah Kategori Baru',
    ],
    'action'     => [
        'create' => 'Tambah Kategori Baru',
        'submit' => 'Simpan',
    ],
    'message'    => [
        'create_success'                 => 'Kategori berhasil disimpan',
        'update_success'                 => 'Kategori berhasil diperbarui',
        'delete_success'                 => 'Kategori berhasil dihapus',
        'delete_failed'                  => 'Kategori gagal dihapus',
        'cannot_delete_default_category' => 'Kategori default tidak bisa dihapus',
    ],
    'attributes' => [
        'name' => 'Label',
        'slug' => 'Slug',
    ],
];
