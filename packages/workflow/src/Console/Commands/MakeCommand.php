<?php

namespace Laravolt\Workflow\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'workflow:make';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create new workflow module';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('Human friendly name');
        $id = $this->ask('Identifier', Str::slug($name));
        $processDefinitionKey = $this->ask('Process definition key');
        $stub = __DIR__ . '/../../../stubs/config.php';
        $target = config_path(sprintf('workflow-modules/%s.php', $id));
        $targetDir = dirname($target);

        $content = File::get($stub);
        foreach (['name', 'processDefinitionKey'] as $var) {
            $content = str_replace('$' . $var, $$var, $content);
        }

        if (! is_dir($targetDir)) {
            File::makeDirectory($targetDir);
        }

        File::put($target, $content);

        Artisan::call('workflow:sync-module', ['--prune' => true]);
    }
}
