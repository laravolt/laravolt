<?php

namespace Laravolt\Workflow;

use Illuminate\Support\Str;
use Laravolt\Workflow\Jobs\CreateSharWorkflowJob;
use Laravolt\Workflow\Jobs\LaunchSharWorkflowInstanceJob;
use Laravolt\Workflow\Jobs\SyncSharWorkflowInstanceJob;
use Laravolt\Workflow\Models\SharWorkflow;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class SharAsyncWorkflowService
{
    /**
     * Create a new BPMN workflow asynchronously
     */
    public function createWorkflowAsync(
        string $name,
        string $bpmnXml,
        ?string $description = null,
        ?int $createdBy = null,
        ?string $callbackUrl = null,
        string $queue = 'shar-workflows'
    ): string {
        $requestId = Str::uuid()->toString();

        CreateSharWorkflowJob::dispatch(
            $name,
            $bpmnXml,
            $description,
            $createdBy,
            $callbackUrl
        )->onQueue($queue)->delay(now()->addSeconds(1));

        \Log::info('SHAR workflow creation job queued', [
            'workflow_name' => $name,
            'created_by' => $createdBy,
            'request_id' => $requestId,
            'queue' => $queue,
        ]);

        return $requestId;
    }

    /**
     * Launch a workflow instance asynchronously
     */
    public function launchWorkflowInstanceAsync(
        string $workflowName,
        array $variables = [],
        ?int $createdBy = null,
        ?string $callbackUrl = null,
        string $queue = 'shar-instances'
    ): string {
        $requestId = Str::uuid()->toString();

        LaunchSharWorkflowInstanceJob::dispatch(
            $workflowName,
            $variables,
            $createdBy,
            $callbackUrl,
            $requestId
        )->onQueue($queue);

        \Log::info('SHAR workflow instance launch job queued', [
            'workflow_name' => $workflowName,
            'created_by' => $createdBy,
            'request_id' => $requestId,
            'queue' => $queue,
        ]);

        return $requestId;
    }

    /**
     * Sync workflow instance asynchronously
     */
    public function syncWorkflowInstanceAsync(
        string $instanceId,
        ?string $callbackUrl = null,
        string $queue = 'shar-sync'
    ): void {
        SyncSharWorkflowInstanceJob::dispatch(
            $instanceId,
            $callbackUrl
        )->onQueue($queue);

        \Log::debug('SHAR workflow instance sync job queued', [
            'instance_id' => $instanceId,
            'queue' => $queue,
        ]);
    }

    /**
     * Batch sync multiple instances asynchronously
     */
    public function batchSyncInstancesAsync(
        array $instanceIds,
        string $queue = 'shar-sync',
        int $delay = 0
    ): void {
        foreach ($instanceIds as $index => $instanceId) {
            SyncSharWorkflowInstanceJob::dispatch($instanceId)
                ->onQueue($queue)
                ->delay(now()->addSeconds($delay * $index));
        }

        \Log::info('SHAR batch sync jobs queued', [
            'instance_count' => count($instanceIds),
            'queue' => $queue,
            'delay_between_jobs' => $delay,
        ]);
    }

    /**
     * Sync all running instances asynchronously
     */
    public function syncAllRunningInstancesAsync(
        string $queue = 'shar-sync',
        int $batchSize = 50,
        int $delayBetweenBatches = 30
    ): int {
        $runningInstances = SharWorkflowInstance::where('status', 'running')
            ->pluck('id')
            ->toArray();

        if (empty($runningInstances)) {
            return 0;
        }

        $batches = array_chunk($runningInstances, $batchSize);
        
        foreach ($batches as $batchIndex => $batch) {
            foreach ($batch as $instanceIndex => $instanceId) {
                SyncSharWorkflowInstanceJob::dispatch($instanceId)
                    ->onQueue($queue)
                    ->delay(now()->addSeconds(($batchIndex * $delayBetweenBatches) + $instanceIndex));
            }
        }

        \Log::info('SHAR running instances sync jobs queued', [
            'total_instances' => count($runningInstances),
            'batches' => count($batches),
            'batch_size' => $batchSize,
            'queue' => $queue,
        ]);

        return count($runningInstances);
    }

    /**
     * Get workflow creation status by checking database
     */
    public function getWorkflowCreationStatus(string $workflowName): array
    {
        $workflow = SharWorkflow::getLatestVersion($workflowName);
        
        if (!$workflow) {
            return [
                'status' => 'not_found',
                'message' => 'Workflow not found or creation still in progress',
            ];
        }

        return [
            'status' => 'created',
            'workflow' => [
                'id' => $workflow->id,
                'name' => $workflow->name,
                'version' => $workflow->version,
                'status' => $workflow->status,
                'created_at' => $workflow->created_at,
            ],
        ];
    }

    /**
     * Get instance launch status
     */
    public function getInstanceLaunchStatus(string $requestId): array
    {
        // In a real implementation, you might store request tracking in a separate table
        // For now, we'll return a generic response
        return [
            'status' => 'queued',
            'message' => 'Instance launch request is being processed',
            'request_id' => $requestId,
        ];
    }

    /**
     * Schedule periodic sync for all running instances
     */
    public function schedulePeriodicSync(
        int $intervalMinutes = 5,
        string $queue = 'shar-sync'
    ): void {
        // This would typically be called from a scheduled job
        $this->syncAllRunningInstancesAsync($queue);

        \Log::info('Scheduled periodic SHAR sync', [
            'interval_minutes' => $intervalMinutes,
            'queue' => $queue,
        ]);
    }

    /**
     * Get queue job statistics
     */
    public function getQueueStatistics(): array
    {
        // This would require a queue monitoring package or custom implementation
        // For now, return basic info
        return [
            'queues' => [
                'shar-workflows' => 'Workflow creation jobs',
                'shar-instances' => 'Instance launch jobs', 
                'shar-sync' => 'Instance sync jobs',
            ],
            'note' => 'Use queue monitoring tools for detailed statistics',
        ];
    }

    /**
     * Create workflow with immediate local storage and async SHAR creation
     */
    public function createWorkflowWithPlaceholder(
        string $name,
        string $bpmnXml,
        ?string $description = null,
        ?int $createdBy = null,
        ?string $callbackUrl = null
    ): SharWorkflow {
        // Create placeholder workflow immediately
        $workflow = SharWorkflow::create([
            'name' => $name,
            'bpmn_xml' => $bpmnXml,
            'description' => $description,
            'version' => 1,
            'status' => 'creating', // Temporary status
            'created_by' => $createdBy,
        ]);

        // Queue the actual SHAR creation
        CreateSharWorkflowJob::dispatch(
            $name,
            $bpmnXml,
            $description,
            $createdBy,
            $callbackUrl
        )->onQueue('shar-workflows');

        return $workflow;
    }

    /**
     * Launch instance with immediate local storage and async SHAR launch
     */
    public function launchInstanceWithPlaceholder(
        string $workflowName,
        array $variables = [],
        ?int $createdBy = null,
        ?string $callbackUrl = null
    ): SharWorkflowInstance {
        // Create placeholder instance immediately
        $instanceId = Str::uuid()->toString();
        
        $instance = SharWorkflowInstance::create([
            'id' => $instanceId,
            'workflow_name' => $workflowName,
            'status' => 'launching', // Temporary status
            'variables' => $variables,
            'started_at' => now(),
            'created_by' => $createdBy,
        ]);

        // Queue the actual SHAR launch
        LaunchSharWorkflowInstanceJob::dispatch(
            $workflowName,
            $variables,
            $createdBy,
            $callbackUrl,
            $instanceId
        )->onQueue('shar-instances');

        return $instance;
    }
}