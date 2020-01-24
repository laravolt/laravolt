<?php

namespace Laravolt\Workflow\Console\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Models\Module;

class SyncModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:sync-module {--prune}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync module definitions';

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
        if (config('workflow-modules') != null) {
            foreach (config('workflow-modules') as $key => $module) {
                if ($this->validateModule($module)) {
                    Module::updateOrCreate(
                        ['key' => $key],
                        ['label' => $module['label'], 'process_definition_key' => $module['process_definition_key']]
                    );
                }
            }
        }

        if ($this->option('prune')) {
            $keys = collect(config('workflow-modules'))->keys();
            $deleted = 0;
            if ($keys->isNotEmpty()) {
                $deleted = Module::query()->whereNotIn('key', $keys)->delete();
            }
            $this->info(sprintf('%d modules pruned', $deleted));
        }
    }

    protected function validateModule($module)
    {
        return is_array($module);
    }
}
