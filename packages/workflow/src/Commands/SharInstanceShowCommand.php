<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharInstanceShowCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:instance:show 
                            {instanceId : The workflow instance ID}
                            {--sync : Sync with SHAR before showing}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     */
    protected $description = 'Show detailed information about a SHAR workflow instance';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $instanceId = $this->argument('instanceId');
        $sync = $this->option('sync');
        $format = $this->option('format');

        try {
            // Sync with SHAR if requested
            if ($sync) {
                $this->info('Syncing with SHAR server...');
                $instance = $sharService->syncWorkflowInstance($instanceId);
            } else {
                $instance = $sharService->getWorkflowInstance($instanceId);
            }

            if (!$instance) {
                $this->error("Workflow instance not found: {$instanceId}");
                return Command::FAILURE;
            }

            if ($format === 'json') {
                $data = [
                    'id' => $instance->id,
                    'workflow_name' => $instance->workflow_name,
                    'status' => $instance->status,
                    'variables' => $instance->variables,
                    'started_at' => $instance->started_at?->toISOString(),
                    'completed_at' => $instance->completed_at?->toISOString(),
                    'duration_seconds' => $instance->getDurationInSeconds(),
                    'tracking_code' => $instance->getTrackingCode(),
                ];
                
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
            } else {
                $this->info("Workflow Instance Details:");
                
                // Basic information
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['Instance ID', $instance->id],
                        ['Workflow Name', $instance->workflow_name],
                        ['Status', ucfirst($instance->status)],
                        ['Started At', $instance->started_at ? $instance->started_at->format('Y-m-d H:i:s') : 'N/A'],
                        ['Completed At', $instance->completed_at ? $instance->completed_at->format('Y-m-d H:i:s') : 'N/A'],
                        ['Duration', $instance->getDurationInSeconds() ? $instance->getDurationInSeconds() . ' seconds' : 'N/A'],
                        ['Tracking Code', $instance->getTrackingCode()],
                    ]
                );

                // Variables
                if (!empty($instance->variables)) {
                    $this->info("\nVariables:");
                    $variableData = [];
                    foreach ($instance->variables as $key => $value) {
                        $variableData[] = [
                            $key,
                            is_array($value) || is_object($value) ? json_encode($value) : (string) $value,
                            gettype($value),
                        ];
                    }
                    
                    $this->table(
                        ['Key', 'Value', 'Type'],
                        $variableData
                    );
                } else {
                    $this->info("\nNo variables set for this instance.");
                }

                // Workflow information
                if ($instance->workflow) {
                    $this->info("\nWorkflow Information:");
                    $this->table(
                        ['Property', 'Value'],
                        [
                            ['Name', $instance->workflow->name],
                            ['Description', $instance->workflow->description ?? 'N/A'],
                            ['Version', "v{$instance->workflow->version}"],
                            ['Status', ucfirst($instance->workflow->status)],
                        ]
                    );
                }
            }

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to get instance details: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}