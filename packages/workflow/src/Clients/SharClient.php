<?php

namespace Laravolt\Workflow\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Laravolt\Workflow\Exceptions\SharException;

class SharClient
{
    protected Client $httpClient;
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('workflow.shar.base_url', 'http://localhost:8080');
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('workflow.shar.timeout', 30),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a new BPMN workflow in SHAR
     */
    public function createWorkflow(string $name, string $bpmnXml): array
    {
        try {
            $response = $this->httpClient->post('/api/v1/workflows', [
                'json' => [
                    'name' => $name,
                    'bpmn_xml' => $bpmnXml,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('SHAR workflow created', [
                'workflow_name' => $name,
                'response' => $data,
            ]);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Failed to create SHAR workflow', [
                'workflow_name' => $name,
                'error' => $e->getMessage(),
            ]);
            
            throw new SharException("Failed to create workflow: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Launch a workflow instance
     */
    public function launchWorkflowInstance(string $workflowName, array $variables = []): array
    {
        try {
            $response = $this->httpClient->post("/api/v1/workflows/{$workflowName}/instances", [
                'json' => [
                    'workflow_name' => $workflowName,
                    'variables' => $variables,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('SHAR workflow instance launched', [
                'workflow_name' => $workflowName,
                'instance_id' => $data['id'] ?? null,
                'variables' => $variables,
            ]);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Failed to launch SHAR workflow instance', [
                'workflow_name' => $workflowName,
                'error' => $e->getMessage(),
            ]);
            
            throw new SharException("Failed to launch workflow instance: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Get all workflows
     */
    public function getWorkflows(): array
    {
        try {
            $response = $this->httpClient->get('/api/v1/workflows');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to get SHAR workflows', ['error' => $e->getMessage()]);
            throw new SharException("Failed to get workflows: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Get a specific workflow
     */
    public function getWorkflow(string $name): array
    {
        try {
            $response = $this->httpClient->get("/api/v1/workflows/{$name}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to get SHAR workflow', [
                'workflow_name' => $name,
                'error' => $e->getMessage(),
            ]);
            throw new SharException("Failed to get workflow: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Delete a workflow
     */
    public function deleteWorkflow(string $name): bool
    {
        try {
            $this->httpClient->delete("/api/v1/workflows/{$name}");
            
            Log::info('SHAR workflow deleted', ['workflow_name' => $name]);
            return true;
        } catch (GuzzleException $e) {
            Log::error('Failed to delete SHAR workflow', [
                'workflow_name' => $name,
                'error' => $e->getMessage(),
            ]);
            throw new SharException("Failed to delete workflow: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Get all workflow instances
     */
    public function getWorkflowInstances(): array
    {
        try {
            $response = $this->httpClient->get('/api/v1/instances');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to get SHAR workflow instances', ['error' => $e->getMessage()]);
            throw new SharException("Failed to get workflow instances: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Get a specific workflow instance
     */
    public function getWorkflowInstance(string $instanceId): array
    {
        try {
            $response = $this->httpClient->get("/api/v1/instances/{$instanceId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to get SHAR workflow instance', [
                'instance_id' => $instanceId,
                'error' => $e->getMessage(),
            ]);
            throw new SharException("Failed to get workflow instance: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Complete a workflow instance
     */
    public function completeWorkflowInstance(string $instanceId): bool
    {
        try {
            $this->httpClient->post("/api/v1/instances/{$instanceId}/complete");
            
            Log::info('SHAR workflow instance completed', ['instance_id' => $instanceId]);
            return true;
        } catch (GuzzleException $e) {
            Log::error('Failed to complete SHAR workflow instance', [
                'instance_id' => $instanceId,
                'error' => $e->getMessage(),
            ]);
            throw new SharException("Failed to complete workflow instance: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Check SHAR server health
     */
    public function healthCheck(): array
    {
        try {
            $response = $this->httpClient->get('/health');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('SHAR health check failed', ['error' => $e->getMessage()]);
            throw new SharException("SHAR health check failed: {$e->getMessage()}", $e->getCode(), $e);
        }
    }
}