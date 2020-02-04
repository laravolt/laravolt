<?php

namespace Laravolt\Workflow\Console\Commands;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Laravolt\Camunda\Models\Deployment;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Workflow\Models\CamundaForm;
use SimpleXMLElement;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'workflow:deploy {name}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Deploy BPMN file to Camunda Server';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $files = collect(File::allFiles(resource_path('bpmn')))
            ->map(function ($item) {
                return $item->getPathname();
            })->sort();

        $choices = $files->prepend('Semua')->toArray();

        $choice = $this->choice('Pilih file BPMN yang akan di-deploy:', $choices);

        if ($choice === 'Semua') {
            $files->shift();
            $filesToBeDeployed = $files->toArray();
        } else {
            $filesToBeDeployed = [$choice];
        }
        $deployName = $this->argument('name');

        try {
            $result = (new Deployment())->create($deployName, $filesToBeDeployed);
            $this->info('Deployment ID '.$result->id);

            $info = [];
            foreach ($result->deployedProcessDefinitions as $processDefinition) {
                $info[] = [$processDefinition->resource, $processDefinition->id, $processDefinition->key];
            }
            $this->table(['BPMN File', 'Process Definition ID', 'Process Definition Key'], $info);
        } catch (ServerException|ClientException $e) {
            $this->error((string) $e->getResponse()->getBody());
        }
    }
}
