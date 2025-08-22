<?php

namespace Laravolt\Workflow;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravolt\Workflow\Clients\SharClient;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflow;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class SharWorkflowService
{
    protected SharClient $sharClient;

    public function __construct(SharClient $sharClient)
    {
        $this->sharClient = $sharClient;
    }

    /**
     * Create a new BPMN workflow
     */
    public function createWorkflow(string $name, string $bpmnXml, ?string $description = null, ?int $createdBy = null): SharWorkflow
    {
        return DB::transaction(function () use ($name, $bpmnXml, $description, $createdBy) {
            // Create workflow in SHAR
            $sharResponse = $this->sharClient->createWorkflow($name, $bpmnXml);

            // Store workflow in Laravel database
            $workflow = SharWorkflow::createNewVersion($name, $bpmnXml, $description, $createdBy);

            Log::info('SHAR workflow created and stored', [
                'workflow_id' => $workflow->id,
                'workflow_name' => $name,
                'version' => $workflow->version,
            ]);

            return $workflow;
        });
    }

    /**
     * Launch a workflow instance
     */
    public function launchWorkflowInstance(string $workflowName, array $variables = [], ?int $createdBy = null): SharWorkflowInstance
    {
        return DB::transaction(function () use ($workflowName, $variables, $createdBy) {
            // Check if workflow exists in Laravel
            $workflow = SharWorkflow::getLatestVersion($workflowName);
            if (!$workflow || !$workflow->isActive()) {
                throw new SharException("Workflow '{$workflowName}' not found or inactive");
            }

            // Launch instance in SHAR
            $sharResponse = $this->sharClient->launchWorkflowInstance($workflowName, $variables);

            // Store instance in Laravel database
            $instance = SharWorkflowInstance::create([
                'id' => $sharResponse['id'],
                'workflow_name' => $workflowName,
                'status' => 'running',
                'variables' => $variables,
                'started_at' => now(),
                'created_by' => $createdBy,
            ]);

            Log::info('SHAR workflow instance launched and stored', [
                'instance_id' => $instance->id,
                'workflow_name' => $workflowName,
            ]);

            return $instance;
        });
    }

    /**
     * Get all workflows
     */
    public function getWorkflows(): \Illuminate\Database\Eloquent\Collection
    {
        return SharWorkflow::with(['instances'])
            ->orderBy('name')
            ->orderBy('version', 'desc')
            ->get()
            ->groupBy('name')
            ->map(function ($workflows) {
                return $workflows->first(); // Get latest version only
            })
            ->values();
    }

    /**
     * Get a specific workflow
     */
    public function getWorkflow(string $name): ?SharWorkflow
    {
        return SharWorkflow::getLatestVersion($name);
    }

    /**
     * Delete a workflow
     */
    public function deleteWorkflow(string $name): bool
    {
        return DB::transaction(function () use ($name) {
            $workflow = SharWorkflow::getLatestVersion($name);
            if (!$workflow) {
                throw new SharException("Workflow '{$name}' not found");
            }

            // Delete from SHAR
            $this->sharClient->deleteWorkflow($name);

            // Deactivate workflow in Laravel (don't actually delete for audit purposes)
            $workflow->deactivate();

            Log::info('SHAR workflow deleted', ['workflow_name' => $name]);

            return true;
        });
    }

    /**
     * Get all workflow instances
     */
    public function getWorkflowInstances(string $workflowName = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = SharWorkflowInstance::with(['workflow']);

        if ($workflowName) {
            $query->where('workflow_name', $workflowName);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get a specific workflow instance
     */
    public function getWorkflowInstance(string $instanceId): ?SharWorkflowInstance
    {
        return SharWorkflowInstance::with(['workflow'])->find($instanceId);
    }

    /**
     * Complete a workflow instance
     */
    public function completeWorkflowInstance(string $instanceId): SharWorkflowInstance
    {
        return DB::transaction(function () use ($instanceId) {
            $instance = SharWorkflowInstance::findOrFail($instanceId);

            if ($instance->isCompleted()) {
                throw new SharException("Workflow instance '{$instanceId}' is already completed");
            }

            // Complete instance in SHAR
            $this->sharClient->completeWorkflowInstance($instanceId);

            // Update instance in Laravel database
            $instance->markCompleted();

            Log::info('SHAR workflow instance completed', [
                'instance_id' => $instanceId,
                'workflow_name' => $instance->workflow_name,
            ]);

            return $instance;
        });
    }

    /**
     * Sync workflow instance status from SHAR
     */
    public function syncWorkflowInstance(string $instanceId): SharWorkflowInstance
    {
        $instance = SharWorkflowInstance::findOrFail($instanceId);

        try {
            // Get latest status from SHAR
            $sharResponse = $this->sharClient->getWorkflowInstance($instanceId);

            // Update local instance
            $instance->update([
                'status' => $sharResponse['status'],
                'variables' => $sharResponse['variables'] ?? $instance->variables,
            ]);

            if ($sharResponse['status'] === 'completed' && !$instance->completed_at) {
                $instance->markCompleted();
            }

            Log::debug('SHAR workflow instance synced', [
                'instance_id' => $instanceId,
                'status' => $sharResponse['status'],
            ]);

        } catch (SharException $e) {
            Log::warning('Failed to sync SHAR workflow instance', [
                'instance_id' => $instanceId,
                'error' => $e->getMessage(),
            ]);
        }

        return $instance->fresh();
    }

    /**
     * Check SHAR server health
     */
    public function checkHealth(): array
    {
        try {
            return $this->sharClient->healthCheck();
        } catch (SharException $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get workflow statistics
     */
    public function getWorkflowStatistics(string $workflowName = null): array
    {
        $query = SharWorkflowInstance::query();

        if ($workflowName) {
            $query->where('workflow_name', $workflowName);
        }

        $instances = $query->get();

        return [
            'total_instances' => $instances->count(),
            'running_instances' => $instances->where('status', 'running')->count(),
            'completed_instances' => $instances->where('status', 'completed')->count(),
            'failed_instances' => $instances->where('status', 'failed')->count(),
            'average_duration' => $instances->filter(function ($instance) {
                return $instance->getDurationInSeconds() !== null;
            })->avg(function ($instance) {
                return $instance->getDurationInSeconds();
            }),
        ];
    }
}