<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharInstanceCompleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:instance:complete 
                            {instanceId : The workflow instance ID}
                            {--force : Force completion without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Complete a SHAR workflow instance';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $instanceId = $this->argument('instanceId');
        $force = $this->option('force');

        try {
            // Get instance details first
            $instance = $sharService->getWorkflowInstance($instanceId);
            
            if (!$instance) {
                $this->error("Workflow instance not found: {$instanceId}");
                return Command::FAILURE;
            }

            if ($instance->isCompleted()) {
                $this->warn("Workflow instance is already completed.");
                return Command::SUCCESS;
            }

            if ($instance->hasFailed()) {
                $this->warn("Workflow instance has failed and cannot be completed.");
                return Command::FAILURE;
            }

            // Show instance details
            $this->info("Instance Details:");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Instance ID', $instance->id],
                    ['Workflow Name', $instance->workflow_name],
                    ['Status', ucfirst($instance->status)],
                    ['Started At', $instance->started_at ? $instance->started_at->format('Y-m-d H:i:s') : 'N/A'],
                    ['Duration', $instance->getDurationInSeconds() ? $instance->getDurationInSeconds() . ' seconds' : 'N/A'],
                ]
            );

            // Confirm completion unless forced
            if (!$force && !$this->confirm('Are you sure you want to complete this workflow instance?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->info('Completing workflow instance...');

            $completedInstance = $sharService->completeWorkflowInstance($instanceId);

            $this->info("✅ Workflow instance completed successfully!");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Instance ID', $completedInstance->id],
                    ['Status', ucfirst($completedInstance->status)],
                    ['Completed At', $completedInstance->completed_at->format('Y-m-d H:i:s')],
                    ['Total Duration', $completedInstance->getDurationInSeconds() . ' seconds'],
                ]
            );

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to complete workflow instance: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}