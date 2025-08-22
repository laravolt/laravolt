<?php

namespace Laravolt\Workflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravolt\Workflow\SharAsyncWorkflowService;

class PeriodicSharSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 1; // Don't retry periodic jobs

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $batchSize = 50,
        public int $delayBetweenBatches = 30
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SharAsyncWorkflowService $asyncService): void
    {
        Log::info('Starting periodic SHAR sync job', [
            'batch_size' => $this->batchSize,
            'delay_between_batches' => $this->delayBetweenBatches,
        ]);

        try {
            $syncedCount = $asyncService->syncAllRunningInstancesAsync(
                'shar-sync',
                $this->batchSize,
                $this->delayBetweenBatches
            );

            Log::info('Periodic SHAR sync completed', [
                'instances_queued' => $syncedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Periodic SHAR sync failed', [
                'error' => $e->getMessage(),
            ]);

            // Don't re-throw - let the job complete
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['shar', 'periodic', 'sync'];
    }
}