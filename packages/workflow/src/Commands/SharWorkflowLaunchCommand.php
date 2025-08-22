<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharWorkflowLaunchCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:workflow:launch 
                            {name : The workflow name}
                            {--variables= : JSON string of variables}
                            {--user-id= : User ID who launches the workflow}
                            {--wait : Wait for workflow completion}';

    /**
     * The console command description.
     */
    protected $description = 'Launch a SHAR workflow instance';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $name = $this->argument('name');
        $variablesJson = $this->option('variables');
        $userId = $this->option('user-id');
        $wait = $this->option('wait');

        // Parse variables
        $variables = [];
        if ($variablesJson) {
            $variables = json_decode($variablesJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON format for variables');
                return Command::FAILURE;
            }
        }

        $this->info("Launching workflow '{$name}'...");

        if (!empty($variables)) {
            $this->info('Variables:');
            foreach ($variables as $key => $value) {
                $this->line("  {$key}: " . json_encode($value));
            }
        }

        try {
            $instance = $sharService->launchWorkflowInstance(
                $name,
                $variables,
                $userId ? (int) $userId : null
            );

            $this->info("✅ Workflow instance launched successfully!");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Instance ID', $instance->id],
                    ['Workflow Name', $instance->workflow_name],
                    ['Status', $instance->status],
                    ['Started At', $instance->started_at->format('Y-m-d H:i:s')],
                    ['Tracking Code', $instance->getTrackingCode()],
                ]
            );

            if ($wait) {
                $this->info('Waiting for workflow completion...');
                $this->waitForCompletion($sharService, $instance->id);
            }

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to launch workflow: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Wait for workflow completion
     */
    private function waitForCompletion(SharWorkflowService $sharService, string $instanceId): void
    {
        $maxWaitTime = 300; // 5 minutes
        $startTime = time();
        
        while (time() - $startTime < $maxWaitTime) {
            try {
                $instance = $sharService->syncWorkflowInstance($instanceId);
                
                if ($instance->isCompleted()) {
                    $duration = $instance->getDurationInSeconds();
                    $this->info("✅ Workflow completed successfully in {$duration} seconds!");
                    return;
                } elseif ($instance->hasFailed()) {
                    $this->error("❌ Workflow failed!");
                    $failureReason = $instance->getVariable('failure_reason');
                    if ($failureReason) {
                        $this->error("Reason: {$failureReason}");
                    }
                    return;
                }

                $this->line("Status: {$instance->status} (waiting...)");
                sleep(5);

            } catch (SharException $e) {
                $this->error("Error checking workflow status: {$e->getMessage()}");
                return;
            }
        }

        $this->warn("⏰ Timeout waiting for workflow completion");
    }
}