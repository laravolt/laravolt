<?php

namespace Laravolt\Workflow\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravolt\Workflow\Models\SharWorkflowInstance;
use Laravolt\Workflow\SharWorkflowService;

class SharCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:cleanup 
                            {--days=30 : Delete instances older than specified days}
                            {--status=completed : Status of instances to cleanup (completed, failed, cancelled)}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force cleanup without confirmation}
                            {--batch-size=100 : Number of instances to process in each batch}';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup old SHAR workflow instances';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $status = $this->option('status');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $batchSize = (int) $this->option('batch-size');

        if ($days < 1) {
            $this->error('Days must be at least 1');
            return Command::FAILURE;
        }

        $cutoffDate = Carbon::now()->subDays($days);

        // Build query
        $query = SharWorkflowInstance::query()
            ->where('completed_at', '<', $cutoffDate)
            ->where('status', $status);

        $totalInstances = $query->count();

        if ($totalInstances === 0) {
            $this->info("No instances found to cleanup (older than {$days} days with status '{$status}').");
            return Command::SUCCESS;
        }

        $this->info("Found {$totalInstances} instance(s) to cleanup:");
        $this->line("- Status: {$status}");
        $this->line("- Older than: {$days} days ({$cutoffDate->format('Y-m-d H:i:s')})");
        $this->line("- Batch size: {$batchSize}");

        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No instances will be deleted");
            
            // Show breakdown by workflow
            $breakdown = $query->selectRaw('workflow_name, COUNT(*) as count')
                ->groupBy('workflow_name')
                ->get();

            if ($breakdown->isNotEmpty()) {
                $this->info("\nBreakdown by workflow:");
                $this->table(
                    ['Workflow Name', 'Instances to Delete'],
                    $breakdown->map(function ($item) {
                        return [$item->workflow_name, $item->count];
                    })->toArray()
                );
            }

            return Command::SUCCESS;
        }

        // Confirm deletion unless forced
        if (!$force && !$this->confirm("Are you sure you want to delete {$totalInstances} instance(s)?")) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Starting cleanup...');
        $progressBar = $this->output->createProgressBar($totalInstances);
        $progressBar->start();

        $deleted = 0;
        $failed = 0;

        // Process in batches
        while (true) {
            $instances = $query->limit($batchSize)->get();
            
            if ($instances->isEmpty()) {
                break;
            }

            foreach ($instances as $instance) {
                try {
                    $instance->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $failed++;
                    \Log::error("Failed to delete instance {$instance->id}: {$e->getMessage()}");
                }
                
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Cleanup completed!");
        $this->table(
            ['Result', 'Count'],
            [
                ['Deleted Successfully', $deleted],
                ['Failed to Delete', $failed],
                ['Total Processed', $deleted + $failed],
            ]
        );

        if ($failed > 0) {
            $this->warn("⚠️  {$failed} instances failed to delete. Check logs for details.");
        }

        // Show storage saved
        $this->info("💾 Database cleanup completed. Consider running 'php artisan optimize' to refresh caches.");

        return Command::SUCCESS;
    }
}