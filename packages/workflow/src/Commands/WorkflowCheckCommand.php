<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Camunda\Http\CamundaClient;

class WorkflowCheckCommand extends Command
{
    protected $signature = 'laravolt:workflow:check';

    public function handle()
    {
        $request = CamundaClient::make()->get('version');

        if ($request->successful()) {
            $this->info(sprintf('Connected to Camunda REST API %s', $request->json('version')));
        } else {
            $this->error('Connection to Camunda REST API failed, please check your configuration:');
            $this->table(
                ['Config', 'Value'],
                collect(config('services.camunda'))->transform(fn ($value, $key) => [$key, $value])->toArray()
            );
        }

        return 0;
    }
}
