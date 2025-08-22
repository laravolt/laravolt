<?php

namespace Laravolt\Workflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravolt\Workflow\Clients\SharClient;
use Laravolt\Workflow\Events\SharWorkflowInstanceLaunched;
use Laravolt\Workflow\Events\SharWorkflowInstanceLaunchFailed;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflow;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class LaunchSharWorkflowInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [5, 15, 30]; // Retry delays in seconds

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $workflowName,
        public array $variables = [],
        public ?int $createdBy = null,
        public ?string $callbackUrl = null,
        public ?string $requestId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SharClient $sharClient): void
    {
        try {
            Log::info('Launching SHAR workflow instance', [
                'workflow_name' => $this->workflowName,
                'created_by' => $this->createdBy,
                'request_id' => $this->requestId,
            ]);

            // Check if workflow exists and is active
            $workflow = SharWorkflow::getLatestVersion($this->workflowName);
            if (!$workflow || !$workflow->isActive()) {
                throw new SharException("Workflow '{$this->workflowName}' not found or inactive");
            }

            // Launch instance in SHAR
            $sharResponse = $sharClient->launchWorkflowInstance($this->workflowName, $this->variables);

            // Store instance in Laravel database
            $instance = SharWorkflowInstance::create([
                'id' => $sharResponse['id'],
                'workflow_name' => $this->workflowName,
                'status' => 'running',
                'variables' => $this->variables,
                'started_at' => now(),
                'created_by' => $this->createdBy,
            ]);

            Log::info('SHAR workflow instance launched successfully', [
                'instance_id' => $instance->id,
                'workflow_name' => $this->workflowName,
                'request_id' => $this->requestId,
            ]);

            // Dispatch success event
            SharWorkflowInstanceLaunched::dispatch($instance, $sharResponse);

            // Send callback if provided
            if ($this->callbackUrl) {
                $this->sendCallback('success', [
                    'instance' => $instance,
                    'shar_response' => $sharResponse,
                ]);
            }

        } catch (SharException $e) {
            Log::error('Failed to launch SHAR workflow instance', [
                'workflow_name' => $this->workflowName,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'request_id' => $this->requestId,
            ]);

            // Dispatch failure event
            SharWorkflowInstanceLaunchFailed::dispatch(
                $this->workflowName,
                $this->variables,
                $e->getMessage(),
                $this->createdBy
            );

            // Send callback if provided
            if ($this->callbackUrl) {
                $this->sendCallback('failed', [
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts(),
                ]);
            }

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SHAR workflow instance launch job failed permanently', [
            'workflow_name' => $this->workflowName,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'request_id' => $this->requestId,
        ]);

        // Dispatch final failure event
        SharWorkflowInstanceLaunchFailed::dispatch(
            $this->workflowName,
            $this->variables,
            $exception->getMessage(),
            $this->createdBy
        );

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
                    'job_type' => 'launch_instance',
                    'workflow_name' => $this->workflowName,
                    'status' => $status,
                    'data' => $data,
                    'request_id' => $this->requestId,
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
        return ['shar', 'instance', 'launch', $this->workflowName];
    }
}