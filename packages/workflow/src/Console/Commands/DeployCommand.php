<?php

namespace Laravolt\Workflow\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravolt\Camunda\Models\Deployment;
use Laravolt\Workflow\Models\Bpmn;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:deploy {name} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy BPMN file to Camunda Server and then import form definition and table structure';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = collect(File::allFiles(resource_path('bpmn')))
            ->map(function ($item) {
                return $item->getPathname();
            })->sort();

        $choices = $files->prepend('all')->toArray();

        if ($this->option('all')) {
            $choice = 'all';
        } else {
            $choice = $this->choice('Pilih file BPMN yang akan di-deploy:', $choices);
        }

        if ($choice === 'all') {
            $files->shift();
            $filesToBeDeployed = $files;
        } else {
            $filesToBeDeployed = collect([$choice]);
        }

        $deployedBpmn = Bpmn::all();

        $filesToBeDeployed = $filesToBeDeployed->reject(function ($file) use ($deployedBpmn) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
            $lastDeployed = $deployedBpmn->where('filename', basename($file))->first()->deployed_at ?? false;
            if (!$lastDeployed) {
                return false;
            }

            return $lastModified->isBefore(Carbon::parse($lastDeployed));
        });

        try {
            $deployName = $this->argument('name');
            $result = (new Deployment())->create($deployName, $filesToBeDeployed->toArray());
            $this->info('Deployment ID '.$result->id);

            $info = [];
            foreach ($result->deployedProcessDefinitions as $processDefinition) {
                Bpmn::updateOrCreate(
                    ['filename' => $processDefinition->resource],
                    [
                        'process_definition_id' => $processDefinition->id,
                        'process_definition_key' => $processDefinition->key,
                        'version' => $processDefinition->version,
                        'deployment_id' => $processDefinition->deploymentId,
                        'deployed_at' => $result->deploymentTime,
                    ]
                );
                $info[] = [$processDefinition->resource, $processDefinition->id, $processDefinition->key];
            }
            $this->table(['BPMN File', 'Process Definition ID', 'Process Definition Key'], $info);
        } catch (ServerException | ClientException $e) {
            $this->error((string) $e->getResponse()->getBody());
        }
    }
}
