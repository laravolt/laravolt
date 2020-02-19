<?php

namespace Laravolt\Workflow\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravolt\Camunda\Models\Deployment;
use Laravolt\Workflow\Models\Bpmn;
use ZipStream\Exception;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:deploy {name} {--all} {--fresh}';

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
        if ($this->option('fresh')) {
            Bpmn::query()->delete();
        }

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

        $existingBpmn = Bpmn::all();

        $filesToBeDeployed = $filesToBeDeployed->reject(function ($file) use ($existingBpmn) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
            $lastDeployed = $existingBpmn->where('filename', basename($file))->first()->deployed_at ?? false;
            if (!$lastDeployed) {
                return false;
            }

            return $lastModified->isBefore(Carbon::parse($lastDeployed));
        });

        $deployedBpmn = collect();
        if ($filesToBeDeployed->isNotEmpty()) {
            try {
                $deployName = $this->argument('name');
                $result = (new Deployment())->create($deployName, $filesToBeDeployed->toArray());
                $this->info('Deployment ID '.$result->id);

                foreach ($result->deployedProcessDefinitions as $processDefinition) {
                    try {
                        $deployedAt = Carbon::parse($result->deploymentTime);
                    } catch (Exception $e) {
                        $deployedAt = now();
                    }

                    Bpmn::updateOrCreate(
                        ['filename' => $processDefinition->resource],
                        [
                            'process_definition_id' => $processDefinition->id,
                            'process_definition_key' => $processDefinition->key,
                            'version' => $processDefinition->version,
                            'nodes' => $this->getNodes(resource_path('bpmn/'.$processDefinition->resource)),
                            'deployment_id' => $processDefinition->deploymentId,
                            'deployed_at' => $deployedAt,
                        ]
                    );
                    $deployedBpmn->push($processDefinition);
                }
            } catch (ServerException | ClientException $e) {
                $this->error((string) $e->getResponse()->getBody());
            }
        }

        $result = Bpmn::all()->transform(function ($item) use ($existingBpmn, $deployedBpmn) {
            if ($existingBpmn->firstWhere('filename', $item->filename)) {
                if ($deployedBpmn->firstWhere('resource', $item->filename)) {
                    $status = '<fg=yellow>Updated</>';
                } elseif (!file_exists(resource_path("bpmn/{$item->filename}"))) {
                    $status = '<fg=red>Deleted</>';
                } else {
                    $status = '<fg=white>No Modification</>';
                }
            } else {
                $status = '<fg=green>New</>';
            }

            return [
                'filename' => $item->filename,
                'process_definition_id' => $item->process_definition_id,
                'process_definition_key' => $item->process_definition_key,
                'status' => $status,
            ];
        });

        $this->table(['BPMN File', 'Process Definition ID', 'Process Definition Key', 'Status'], $result->toArray());

        foreach ($result->toArray() as $bpmn) {
            if (Str::contains($bpmn['status'], ['New', 'Updated'])) {
                $this->call('workflow:import', ['key' => $bpmn['process_definition_key']]);
            }
        }
    }

    protected function getNodes($file): array
    {
        $xml = new \SimpleXMLElement($file, 0, true);
        $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $xml->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');

        $nodesMapping = [
            'startEvent' => '//bpmn:startEvent',
            'userTask' => '//bpmn:userTask',
            'callActivity' => '//bpmn:callActivity',
        ];

        $nodes = [];
        foreach ($nodesMapping as $type => $selector) {
            $elements = $xml->xpath($selector);
            foreach ($elements as $element) {
                $id = (string) $element->attributes()['id'] ?? null;
                if ($id) {
                    $nodes[] = [
                        'type' => $type,
                        'id' => $id,
                    ];
                }
            }
        }

        return $nodes;
    }
}
