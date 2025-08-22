<?php

namespace Laravolt\Workflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravolt\Workflow\Clients\SharClient;
use Laravolt\Workflow\Events\SharWorkflowCreated;
use Laravolt\Workflow\Events\SharWorkflowCreationFailed;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflow;

class CreateSharWorkflowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Retry delays in seconds

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $name,
        public string $bpmnXml,
        public ?string $description = null,
        public ?int $createdBy = null,
        public ?string $callbackUrl = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SharClient $sharClient): void
    {
        try {
            Log::info('Creating SHAR workflow', [
                'workflow_name' => $this->name,
                'created_by' => $this->createdBy,
            ]);

            // Create workflow in SHAR
            $sharResponse = $sharClient->createWorkflow($this->name, $this->bpmnXml);

            // Store workflow in Laravel database
            $workflow = SharWorkflow::createNewVersion(
                $this->name,
                $this->bpmnXml,
                $this->description,
                $this->createdBy
            );

            Log::info('SHAR workflow created successfully', [
                'workflow_id' => $workflow->id,
                'workflow_name' => $this->name,
                'version' => $workflow->version,
            ]);

            // Dispatch success event
            SharWorkflowCreated::dispatch($workflow, $sharResponse);

            // Send callback if provided
            if ($this->callbackUrl) {
                $this->sendCallback('success', [
                    'workflow' => $workflow,
                    'shar_response' => $sharResponse,
                ]);
            }

        } catch (SharException $e) {
            Log::error('Failed to create SHAR workflow', [
                'workflow_name' => $this->name,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Dispatch failure event
            SharWorkflowCreationFailed::dispatch($this->name, $e->getMessage(), $this->createdBy);

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
        Log::error('SHAR workflow creation job failed permanently', [
            'workflow_name' => $this->name,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Dispatch final failure event
        SharWorkflowCreationFailed::dispatch($this->name, $exception->getMessage(), $this->createdBy);

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
                    'job_type' => 'create_workflow',
                    'workflow_name' => $this->name,
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
        return ['shar', 'workflow', 'create', $this->name];
    }
}