<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharBatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:batch 
                            {operation : Operation to perform (import, launch, complete)}
                            {--file= : JSON file with batch data}
                            {--workflow= : Workflow name for batch launch}
                            {--instances= : Comma-separated instance IDs for batch operations}
                            {--variables= : JSON string of variables for batch launch}
                            {--count=1 : Number of instances to launch}
                            {--dry-run : Show what would be done without executing}
                            {--force : Force operation without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Perform batch operations on SHAR workflows and instances';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $operation = $this->argument('operation');
        
        return match ($operation) {
            'import' => $this->batchImport($sharService),
            'launch' => $this->batchLaunch($sharService),
            'complete' => $this->batchComplete($sharService),
            default => $this->invalidOperation($operation),
        };
    }

    /**
     * Batch import workflows from JSON file
     */
    private function batchImport(SharWorkflowService $sharService): int
    {
        $filePath = $this->option('file');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (!$filePath) {
            $this->error('--file option is required for import operation');
            return Command::FAILURE;
        }

        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $jsonContent = File::get($filePath);
        $workflows = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file format');
            return Command::FAILURE;
        }

        if (!is_array($workflows)) {
            $this->error('JSON file must contain an array of workflows');
            return Command::FAILURE;
        }

        $this->info("Found " . count($workflows) . " workflow(s) to import");

        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No workflows will be imported");
            
            foreach ($workflows as $index => $workflow) {
                $this->line("#{$index + 1}: {$workflow['name']} (v{$workflow['version'] ?? 1})");
            }
            
            return Command::SUCCESS;
        }

        if (!$force && !$this->confirm('Do you want to proceed with the import?')) {
            $this->info('Import cancelled.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar(count($workflows));
        $progressBar->start();

        $imported = 0;
        $failed = 0;

        foreach ($workflows as $workflowData) {
            try {
                $sharService->createWorkflow(
                    $workflowData['name'],
                    $workflowData['bpmn_xml'],
                    $workflowData['description'] ?? null,
                    $workflowData['created_by'] ?? null
                );
                $imported++;
            } catch (SharException $e) {
                $failed++;
                \Log::error("Failed to import workflow {$workflowData['name']}: {$e->getMessage()}");
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Batch import completed!");
        $this->table(
            ['Result', 'Count'],
            [
                ['Imported Successfully', $imported],
                ['Failed', $failed],
                ['Total Processed', $imported + $failed],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Batch launch workflow instances
     */
    private function batchLaunch(SharWorkflowService $sharService): int
    {
        $workflowName = $this->option('workflow');
        $variablesJson = $this->option('variables');
        $count = (int) $this->option('count');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (!$workflowName) {
            $this->error('--workflow option is required for launch operation');
            return Command::FAILURE;
        }

        if ($count < 1 || $count > 1000) {
            $this->error('Count must be between 1 and 1000');
            return Command::FAILURE;
        }

        // Parse variables
        $variables = [];
        if ($variablesJson) {
            $variables = json_decode($variablesJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON format for variables');
                return Command::FAILURE;
            }
        }

        $this->info("Batch launching {$count} instance(s) of workflow '{$workflowName}'");
        
        if (!empty($variables)) {
            $this->line("Variables: " . json_encode($variables));
        }

        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No instances will be launched");
            return Command::SUCCESS;
        }

        if (!$force && !$this->confirm("Launch {$count} instance(s)?")) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $launched = 0;
        $failed = 0;
        $instanceIds = [];

        for ($i = 0; $i < $count; $i++) {
            try {
                // Add instance number to variables
                $instanceVariables = array_merge($variables, ['batch_index' => $i + 1]);
                
                $instance = $sharService->launchWorkflowInstance(
                    $workflowName,
                    $instanceVariables,
                    null
                );
                
                $instanceIds[] = $instance->id;
                $launched++;
            } catch (SharException $e) {
                $failed++;
                \Log::error("Failed to launch instance #{$i + 1}: {$e->getMessage()}");
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Batch launch completed!");
        $this->table(
            ['Result', 'Count'],
            [
                ['Launched Successfully', $launched],
                ['Failed', $failed],
                ['Total Attempted', $launched + $failed],
            ]
        );

        if ($launched > 0 && $this->confirm('Show launched instance IDs?')) {
            $this->info("Launched Instance IDs:");
            foreach ($instanceIds as $id) {
                $this->line("  - {$id}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Batch complete workflow instances
     */
    private function batchComplete(SharWorkflowService $sharService): int
    {
        $instancesString = $this->option('instances');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (!$instancesString) {
            $this->error('--instances option is required for complete operation');
            return Command::FAILURE;
        }

        $instanceIds = array_map('trim', explode(',', $instancesString));
        $instanceIds = array_filter($instanceIds); // Remove empty values

        if (empty($instanceIds)) {
            $this->error('No valid instance IDs provided');
            return Command::FAILURE;
        }

        $this->info("Batch completing " . count($instanceIds) . " instance(s)");

        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No instances will be completed");
            
            foreach ($instanceIds as $index => $instanceId) {
                $this->line("#{$index + 1}: {$instanceId}");
            }
            
            return Command::SUCCESS;
        }

        if (!$force && !$this->confirm('Complete ' . count($instanceIds) . ' instance(s)?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar(count($instanceIds));
        $progressBar->start();

        $completed = 0;
        $failed = 0;
        $alreadyCompleted = 0;

        foreach ($instanceIds as $instanceId) {
            try {
                $instance = $sharService->getWorkflowInstance($instanceId);
                
                if (!$instance) {
                    $failed++;
                    \Log::error("Instance not found: {$instanceId}");
                } elseif ($instance->isCompleted()) {
                    $alreadyCompleted++;
                } else {
                    $sharService->completeWorkflowInstance($instanceId);
                    $completed++;
                }
            } catch (SharException $e) {
                $failed++;
                \Log::error("Failed to complete instance {$instanceId}: {$e->getMessage()}");
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Batch completion finished!");
        $this->table(
            ['Result', 'Count'],
            [
                ['Completed Successfully', $completed],
                ['Already Completed', $alreadyCompleted],
                ['Failed', $failed],
                ['Total Processed', count($instanceIds)],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Handle invalid operation
     */
    private function invalidOperation(string $operation): int
    {
        $this->error("Invalid operation: {$operation}");
        $this->line("Valid operations: import, launch, complete");
        
        $this->info("\nExamples:");
        $this->line("  Import workflows:");
        $this->line("    php artisan shar:batch import --file=workflows.json");
        
        $this->line("\n  Launch multiple instances:");
        $this->line("    php artisan shar:batch launch --workflow=MyWorkflow --count=10");
        
        $this->line("\n  Complete multiple instances:");
        $this->line("    php artisan shar:batch complete --instances=id1,id2,id3");

        return Command::FAILURE;
    }
}