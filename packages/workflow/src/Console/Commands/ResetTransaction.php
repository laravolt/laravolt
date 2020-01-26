<?php

namespace Laravolt\Workflow\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravolt\Workflow\Models\CamundaForm;

class ResetTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:reset-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all transaction table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = CamundaForm::distinct()->pluck('form_name');
        $tables = $tables->merge(config('laravolt.workflow.tables.transaction'));
        $header = $tables->map(function ($item) {
            return [$item];
        });

        $this->warn(sprintf('Melakukan koneksi ke %s@%s', DB::getDatabaseName(), DB::getConfig('host')));
        $this->table(['Tabel'], $header);
        $confirm = $this->confirm('Anda yakin ingin menghapus semua data untuk tabel di atas?');

        if ($confirm) {
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info('✅ '.$table);
                }
            }
        }
    }
}
