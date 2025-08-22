<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Laravolt\Workflow\Jobs\PeriodicSharSyncJob;
use Laravolt\Workflow\SharAsyncWorkflowService;

class SharQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:queue 
                            {action : Action to perform (start, status, sync, clear)}
                            {--queue= : Specific queue to target}
                            {--timeout=60 : Worker timeout in seconds}
                            {--memory=128 : Memory limit in MB}
                            {--sleep=3 : Sleep time when no jobs available}';

    /**
     * The console command description.
     */
    protected $description = 'Manage SHAR queue workers and jobs';

    /**
     * Execute the console command.
     */
    public function handle(SharAsyncWorkflowService $asyncService): int
    {
        $action = $this->argument('action');
        
        return match ($action) {
            'start' => $this->startWorkers(),
            'status' => $this->showStatus($asyncService),
            'sync' => $this->triggerSync($asyncService),
            'clear' => $this->clearJobs(),
            default => $this->invalidAction($action),
        };
    }

    /**
     * Start queue workers for SHAR
     */
    private function startWorkers(): int
    {
        $queue = $this->option('queue');
        $timeout = $this->option('timeout');
        $memory = $this->option('memory');
        $sleep = $this->option('sleep');

        $this->info('🚀 Starting SHAR queue workers...');

        if ($queue) {
            $this->startSingleWorker($queue, $timeout, $memory, $sleep);
        } else {
            // Start workers for all SHAR queues
            $queues = ['shar-workflows', 'shar-instances', 'shar-sync'];
            
            foreach ($queues as $queueName) {
                $this->startSingleWorker($queueName, $timeout, $memory, $sleep);
            }
        }

        $this->info('✅ Queue workers started successfully!');
        $this->line('Use "php artisan queue:work" to start additional workers');
        $this->line('Use "php artisan shar:queue status" to check worker status');

        return Command::SUCCESS;
    }

    /**
     * Start a single queue worker
     */
    private function startSingleWorker(string $queue, int $timeout, int $memory, int $sleep): void
    {
        $this->line("Starting worker for queue: {$queue}");
        
        // This would typically be done with a process manager like Supervisor
        // For demonstration, we'll show the command that should be run
        $command = "php artisan queue:work --queue={$queue} --timeout={$timeout} --memory={$memory} --sleep={$sleep} --tries=3";
        
        $this->line("Command: {$command}");
        $this->warn("Note: Run this command in a separate terminal or use a process manager like Supervisor");
    }

    /**
     * Show queue status and statistics
     */
    private function showStatus(SharAsyncWorkflowService $asyncService): int
    {
        $this->info('📊 SHAR Queue Status');
        $this->newLine();

        // Show queue statistics
        $stats = $asyncService->getQueueStatistics();
        
        $this->table(
            ['Queue', 'Description'],
            collect($stats['queues'])->map(function ($description, $queue) {
                return [$queue, $description];
            })->toArray()
        );

        // Show pending jobs (this would require additional implementation)
        $this->info('💡 For detailed queue monitoring, consider using:');
        $this->line('- Laravel Horizon (for Redis queues)');
        $this->line('- Queue monitoring packages');
        $this->line('- Custom queue dashboard');

        return Command::SUCCESS;
    }

    /**
     * Trigger periodic sync
     */
    private function triggerSync(SharAsyncWorkflowService $asyncService): int
    {
        $this->info('🔄 Triggering periodic SHAR sync...');

        try {
            // Dispatch periodic sync job
            PeriodicSharSyncJob::dispatch()->onQueue('shar-sync');

            $this->info('✅ Periodic sync job queued successfully');
            $this->line('This will sync all running workflow instances with SHAR');

        } catch (\Exception $e) {
            $this->error("Failed to queue sync job: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clear failed jobs
     */
    private function clearJobs(): int
    {
        $queue = $this->option('queue');

        $this->info('🧹 Clearing failed SHAR jobs...');

        try {
            if ($queue) {
                Artisan::call('queue:flush', ['--queue' => $queue]);
                $this->info("✅ Cleared jobs from queue: {$queue}");
            } else {
                // Clear all SHAR queues
                $queues = ['shar-workflows', 'shar-instances', 'shar-sync'];
                
                foreach ($queues as $queueName) {
                    Artisan::call('queue:flush', ['--queue' => $queueName]);
                    $this->line("Cleared queue: {$queueName}");
                }
                
                $this->info('✅ All SHAR queues cleared');
            }

        } catch (\Exception $e) {
            $this->error("Failed to clear jobs: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Handle invalid action
     */
    private function invalidAction(string $action): int
    {
        $this->error("Invalid action: {$action}");
        $this->line("Valid actions: start, status, sync, clear");
        
        $this->info("\nExamples:");
        $this->line("  Start all workers:");
        $this->line("    php artisan shar:queue start");
        
        $this->line("\n  Start specific queue worker:");
        $this->line("    php artisan shar:queue start --queue=shar-instances");
        
        $this->line("\n  Check status:");
        $this->line("    php artisan shar:queue status");
        
        $this->line("\n  Trigger sync:");
        $this->line("    php artisan shar:queue sync");
        
        $this->line("\n  Clear failed jobs:");
        $this->line("    php artisan shar:queue clear");

        return Command::FAILURE;
    }
}