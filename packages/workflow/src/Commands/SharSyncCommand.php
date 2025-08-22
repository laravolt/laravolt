<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflowInstance;
use Laravolt\Workflow\SharWorkflowService;

class SharSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:sync 
                            {--instance= : Sync specific instance ID}
                            {--workflow= : Sync all instances of a specific workflow}
                            {--status=running : Sync instances with specific status}
                            {--force : Force sync without confirmation}
                            {--batch-size=50 : Number of instances to process in each batch}';

    /**
     * The console command description.
     */
    protected $description = 'Sync workflow instances with SHAR server';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $instanceId = $this->option('instance');
        $workflowName = $this->option('workflow');
        $status = $this->option('status');
        $force = $this->option('force');
        $batchSize = (int) $this->option('batch-size');

        try {
            if ($instanceId) {
                return $this->syncSingleInstance($sharService, $instanceId);
            } else {
                return $this->syncMultipleInstances($sharService, $workflowName, $status, $force, $batchSize);
            }
        } catch (SharException $e) {
            $this->error("Sync failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Sync a single instance
     */
    private function syncSingleInstance(SharWorkflowService $sharService, string $instanceId): int
    {
        $this->info("Syncing instance: {$instanceId}");

        try {
            $instance = $sharService->syncWorkflowInstance($instanceId);

            $this->info("✅ Instance synced successfully!");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Instance ID', $instance->id],
                    ['Workflow Name', $instance->workflow_name],
                    ['Status', ucfirst($instance->status)],
                    ['Last Updated', $instance->updated_at->format('Y-m-d H:i:s')],
                ]
            );

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to sync instance: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Sync multiple instances
     */
    private function syncMultipleInstances(
        SharWorkflowService $sharService,
        ?string $workflowName,
        string $status,
        bool $force,
        int $batchSize
    ): int {
        // Build query
        $query = SharWorkflowInstance::query();

        if ($workflowName) {
            $query->where('workflow_name', $workflowName);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $totalInstances = $query->count();

        if ($totalInstances === 0) {
            $this->info('No instances found to sync.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalInstances} instance(s) to sync.");

        // Show what will be synced
        if ($workflowName) {
            $this->line("Workflow: {$workflowName}");
        }
        if ($status) {
            $this->line("Status: {$status}");
        }
        $this->line("Batch size: {$batchSize}");

        // Confirm unless forced
        if (!$force && !$this->confirm('Do you want to proceed with the sync?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($totalInstances);
        $progressBar->start();

        $synced = 0;
        $failed = 0;

        // Process in batches
        $query->chunk($batchSize, function ($instances) use ($sharService, $progressBar, &$synced, &$failed) {
            foreach ($instances as $instance) {
                try {
                    $sharService->syncWorkflowInstance($instance->id);
                    $synced++;
                } catch (SharException $e) {
                    $failed++;
                    // Log error but continue with other instances
                    \Log::error("Failed to sync instance {$instance->id}: {$e->getMessage()}");
                }
                
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Sync completed!");
        $this->table(
            ['Result', 'Count'],
            [
                ['Synced Successfully', $synced],
                ['Failed', $failed],
                ['Total Processed', $synced + $failed],
            ]
        );

        if ($failed > 0) {
            $this->warn("⚠️  {$failed} instances failed to sync. Check logs for details.");
        }

        return Command::SUCCESS;
    }
}