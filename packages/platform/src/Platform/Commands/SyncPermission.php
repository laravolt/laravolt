<?php

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;

class SyncPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:sync-permission {--refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize permission table and config file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Synchronize Permissions Entries');

        $result = app('laravolt.acl')->syncPermission($this->option('refresh'));

        $this->table(['ID', 'Name', 'Status'], $result);
    }
}
