<?php

namespace Laravolt\Workflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravolt\Workflow\Clients\SharClient;
use Laravolt\Workflow\Events\SharWorkflowInstanceSynced;
use Laravolt\Workflow\Events\SharWorkflowInstanceCompleted;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class SyncSharWorkflowInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 2;
    public $backoff = [5, 15]; // Retry delays in seconds

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $instanceId,
        public ?string $callbackUrl = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SharClient $sharClient): void
    {
        try {
            $instance = SharWorkflowInstance::findOrFail($this->instanceId);
            
            Log::debug('Syncing SHAR workflow instance', [
                'instance_id' => $this->instanceId,
                'current_status' => $instance->status,
            ]);

            // Get latest status from SHAR
            $sharResponse = $sharClient->getWorkflowInstance($this->instanceId);
            $previousStatus = $instance->status;

            // Update local instance
            $instance->update([
                'status' => $sharResponse['status'],
                'variables' => $sharResponse['variables'] ?? $instance->variables,
            ]);

            // Mark as completed if status changed to completed
            if ($sharResponse['status'] === 'completed' && !$instance->completed_at) {
                $instance->markCompleted();
                
                // Dispatch completion event
                SharWorkflowInstanceCompleted::dispatch($instance);
                
                Log::info('SHAR workflow instance completed', [
                    'instance_id' => $this->instanceId,
                    'workflow_name' => $instance->workflow_name,
                ]);
            }

            // Dispatch sync event
            SharWorkflowInstanceSynced::dispatch($instance, $previousStatus, $sharResponse['status']);

            // Send callback if provided
            if ($this->callbackUrl) {
                $this->sendCallback('success', [
                    'instance' => $instance,
                    'previous_status' => $previousStatus,
                    'new_status' => $sharResponse['status'],
                ]);
            }

        } catch (SharException $e) {
            Log::warning('Failed to sync SHAR workflow instance', [
                'instance_id' => $this->instanceId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Send callback if provided
            if ($this->callbackUrl) {
                $this->sendCallback('failed', [
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts(),
                ]);
            }

            // Don't re-throw for sync operations - they can fail silently
            // and be retried later
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::warning('SHAR workflow instance sync job failed permanently', [
            'instance_id' => $this->instanceId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Send final callback if provided
        if ($this->callbackUrl) {
            $this->sendCallback('failed_permanently', [
                'error' => $exception->getMessage(),
                'attempts' => $this->attempts(),
            ]);
        }
    }

    /**
     * Send HTTP callback notification
     */
    private function sendCallback(string $status, array $data): void
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            $client->post($this->callbackUrl, [
                'json' => [
                    'job_type' => 'sync_instance',
                    'instance_id' => $this->instanceId,
                    'status' => $status,
                    'data' => $data,
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send callback notification', [
                'callback_url' => $this->callbackUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['shar', 'instance', 'sync', $this->instanceId];
    }
}