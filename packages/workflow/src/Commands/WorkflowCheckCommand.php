<?php

declare(strict_types=1);

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
            $this->table(['Config', 'Value'], $this->configRows((array) config('services.camunda')));
        }

        return 0;
    }

    /**
     * Config rows for the failure table, with credentials masked so the output
     * can be pasted into an issue or a chat without leaking them.
     */
    protected function configRows(array $config): array
    {
        return collect($config)
            ->map(fn ($value, $key) => [
                $key,
                filled($value) && preg_match('/pass|secret|token|key/i', (string) $key) ? '********' : $value,
            ])
            ->values()
            ->all();
    }
}
