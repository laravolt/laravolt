<?php

namespace Laravolt\Workflow\Console\Commands;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Workflow\Models\Module;

class MakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new workflow module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('Human friendly label, akan ditampilkan sebagai di halaman web');

        do {
            $id = $this->ask('Identifier, akan ditampilkan sebagai URL, direkomendasikan dalam slug format',
                Str::slug($name));
            $target = config_path(sprintf('workflow-modules/%s.php', $id));
            $exists = is_file($target);

            if ($exists) {
                $this->error(sprintf('File %s sudah ada', $target));
            }
        } while ($exists);

        do {
            $processDefinitionKey = $this->ask('Process definition key, sesuai yang terdaftar di camunda');
            $processDefinition = null;

            try {
                $processDefinition = ProcessDefinition::byKey($processDefinitionKey)->fetch();
            } catch (ClientException $e) {
                $this->error($e->getMessage());
            }
        } while ($processDefinition === null);

        $stub = __DIR__.'/../../../stubs/config.php';
        $targetDir = dirname($target);

        $content = File::get($stub);
        foreach (['name', 'processDefinitionKey'] as $var) {
            $content = str_replace('$'.$var, $$var, $content);
        }

        if (!is_dir($targetDir)) {
            File::makeDirectory($targetDir);
        }

        File::put($target, $content);

        $this->warn(sprintf('File %s berhasil dibuat, silakan dimodifikasi sesuai kebutuhan dan jangan lupa dicommit ya ☺️',
            $target));

        Module::updateOrCreate(
            ['key' => $id],
            ['label' => $name, 'process_definition_key' => $processDefinitionKey]
        );

        $this->info('Importing form dan tabel...');
        Artisan::call('workflow:import', ['key' => $processDefinitionKey]);
    }
}
