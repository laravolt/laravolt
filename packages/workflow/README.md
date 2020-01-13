# Laravolt Workflow Engine

Sebuah engine untuk berkomunikasi dengan diagram BPMN melalui Camunda REST API untuk membuat aplikasi 2 minggu jadi.



## Instalasi

`composer require laravolt/workflow`



## Konfigurasi REST API

```
# Wajib
CAMUNDA_API_URL=https://<camunda-host>/rest

# Opsional
CAMUNDA_API_TENANT_ID=
CAMUNDA_API_USER=
CAMUNDA_API_PASSWORD=
```



## Membuat Modul Baru

1. Buat sebuah file BPMN
2. Deploy ke server Camunda
3. Jalankan perintah `php artisan workflow:make`
4. Sebuah file `config/workflow-modules/<module.php>` akan digenerate



## Konfigurasi Modul

```php
<?php

  return [
  // sesuai Process Definition Key dari Camunda
  'process_definition_key' => 'registration',

  // Judul modul, akan ditampilkan di tiap halaman
  'label' => 'Registrasi',


  // Definisi tabel dan query untuk menampilkan data
  'table' => \App\TableView\SomeModuleTableView::class,

  // Satu process BPMN bisa memiliki banyak Task Definition Key
  // Dibawah ini adalah whitelist task-task yang akan ditampilkan ketika melihat detail
  // sebuah Process Instance berdasar Module Key di atas
  'whitelist' => [
   	[
      'label' => 'A Task Label',
      'task' => 'task_name_1',

      // hanya tampilkan field-field berikut ini
      'only' => ['field_1', 'field_2'],

      // mutators digunakan untuk mengubah value dari sebuah field yang akan disimpan
      'mutators' => [
        'invoice_no' => [
          \App\Services\InvoiceNumberGenerator::class,
        ],
      ],
    ],
  ],
];

```

## Menampilkan Data

Buat sebuah Table dengan stuktur sebagai berikut:

```php
<?php

declare(strict_types=1);

namespace App\TableView;

use Illuminate\Support\Facades\DB;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Workflow\Tables\Table;

class SomeModuleTableView extends Table
{
  // query untuk menampilkan data, bisa pakai Query Builder, bisa pakai Eloquent
  public function source($sqlOnly = false)
  {
    return DB::table('foo')->paginate();
  }
  
  // definisi kolom, sesuai https://laravolt.dev/docs/suitable/
  protected function columns()
  {
    return [
      Numbering::make('No'),
      Text::make('process_instance_id'),
      
      // dan kolom lainnya sesuai kebutuhan...

      // CRUD buttons
      $this->buttons()
    ];
  }
}

```

## Mutator

Buat sebuah class dengan struktur:

```php
class InvoiceNumberGenerator
{
  public function execute()
  {
   	// do logic
    return $generatedInvoiceNumber;
  }
}

```

