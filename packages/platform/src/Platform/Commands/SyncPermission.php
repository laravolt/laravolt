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
    protected $signature = 'laravolt:sync-permission {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize permission table and config file';

    protected $config;

    /**
     * Create a new command instance.
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
        $this->info('Synchronize Permissions Entries');

        $result = app('laravolt.acl')->syncPermission($this->option('clear'));

        $this->table(['ID', 'Name', 'Status'], $result);
    }
}
