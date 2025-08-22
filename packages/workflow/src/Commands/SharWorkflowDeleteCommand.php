<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharWorkflowDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:workflow:delete 
                            {name : The workflow name}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Delete a SHAR workflow';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        try {
            // Get workflow details first
            $workflow = $sharService->getWorkflow($name);
            
            if (!$workflow) {
                $this->error("Workflow not found: {$name}");
                return Command::FAILURE;
            }

            // Show workflow details and instances
            $this->info("Workflow Details:");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Name', $workflow->name],
                    ['Version', "v{$workflow->version}"],
                    ['Status', ucfirst($workflow->status)],
                    ['Description', $workflow->description ?? 'N/A'],
                    ['Created', $workflow->created_at->format('Y-m-d H:i:s')],
                ]
            );

            // Show instances count
            $instancesCount = $workflow->instances->count();
            $runningInstances = $workflow->instances->where('status', 'running')->count();

            if ($instancesCount > 0) {
                $this->warn("⚠️  This workflow has {$instancesCount} instance(s), including {$runningInstances} running instance(s).");
                
                if ($runningInstances > 0) {
                    $this->warn("Deleting this workflow may affect running instances!");
                }
            }

            // Confirm deletion unless forced
            if (!$force && !$this->confirm("Are you sure you want to delete workflow '{$name}'?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->info('Deleting workflow...');

            $sharService->deleteWorkflow($name);

            $this->info("✅ Workflow '{$name}' deleted successfully!");

            if ($instancesCount > 0) {
                $this->info("ℹ️  Workflow instances are preserved for audit purposes but marked as inactive.");
            }

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to delete workflow: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}