<?php

return [
    // sesuai Process Definition Key dari Camunda
    'process_definition_key' => 'ajudikasi',

    // Judul modul, akan ditampilkan di tiap halaman
    'label' => 'Ajudikator',

    // jika null, akan otomatis didetect dari Start Task Name diagram BPMN
    'start_task_name' => null,

    'table' => \App\TableView\AjudikatorTableView::class,

    // Satu process BPMN bisa memiliki banyak Task Definition Key
    // Dibawah ini adalah whitelist task-task yang akan ditampilkan ketika melihat detail
    // sebuah Process Instance berdasar Module Key di atas
    'whitelist' => [
        [
            'task' => 'start_ajudikasi',
            'attributes' => [
                'active' => true,
            ],
        ],
        [
            'task' => 'permohonan_ajudikasi',
            'attributes' => [
                'active' => false,
            ],
        ],
        [
            'task' => 'telaah_permohonan_ajudikasi',
            'attributes' => [
                'active' => false,
            ],
        ],
        [
            'task' => 'pemberitahuan_hsl_telaah',
            'attributes' => [
                'active' => false,
            ],
        ],
        [
            'task' => 'penetapan_ajudikator',
            'attributes' => [
                'active' => false,
            ],
        ],
        [
            'task' => 'pemanggilan_sidang',
            'attributes' => [
                'active' => false,
            ],
        ],
    ],
];
