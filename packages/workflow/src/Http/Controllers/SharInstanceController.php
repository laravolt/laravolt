<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;
use Illuminate\Support\Facades\Validator;

class SharInstanceController extends Controller
{
    protected SharWorkflowService $sharWorkflowService;

    public function __construct(SharWorkflowService $sharWorkflowService)
    {
        $this->sharWorkflowService = $sharWorkflowService;
    }

    /**
     * Display a listing of workflow instances
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $workflowName = $request->query('workflow_name');
            $instances = $this->sharWorkflowService->getWorkflowInstances($workflowName);
            
            return response()->json([
                'success' => true,
                'data' => $instances->map(function ($instance) {
                    return [
                        'id' => $instance->id,
                        'workflow_name' => $instance->workflow_name,
                        'status' => $instance->status,
                        'variables' => $instance->variables,
                        'started_at' => $instance->started_at,
                        'completed_at' => $instance->completed_at,
                        'duration_seconds' => $instance->getDurationInSeconds(),
                        'tracking_code' => $instance->getTrackingCode(),
                    ];
                }),
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified workflow instance
     */
    public function show(string $instanceId): JsonResponse
    {
        try {
            $instance = $this->sharWorkflowService->getWorkflowInstance($instanceId);
            
            if (!$instance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow instance not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $instance->id,
                    'workflow_name' => $instance->workflow_name,
                    'status' => $instance->status,
                    'variables' => $instance->variables,
                    'started_at' => $instance->started_at,
                    'completed_at' => $instance->completed_at,
                    'duration_seconds' => $instance->getDurationInSeconds(),
                    'tracking_code' => $instance->getTrackingCode(),
                    'workflow' => $instance->workflow ? [
                        'name' => $instance->workflow->name,
                        'description' => $instance->workflow->description,
                        'version' => $instance->workflow->version,
                    ] : null,
                ],
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete a workflow instance
     */
    public function complete(string $instanceId): JsonResponse
    {
        try {
            $instance = $this->sharWorkflowService->completeWorkflowInstance($instanceId);

            return response()->json([
                'success' => true,
                'message' => 'Workflow instance completed successfully',
                'data' => [
                    'id' => $instance->id,
                    'workflow_name' => $instance->workflow_name,
                    'status' => $instance->status,
                    'completed_at' => $instance->completed_at,
                ],
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync workflow instance with SHAR
     */
    public function sync(string $instanceId): JsonResponse
    {
        try {
            $instance = $this->sharWorkflowService->syncWorkflowInstance($instanceId);

            return response()->json([
                'success' => true,
                'message' => 'Workflow instance synced successfully',
                'data' => [
                    'id' => $instance->id,
                    'workflow_name' => $instance->workflow_name,
                    'status' => $instance->status,
                    'variables' => $instance->variables,
                    'started_at' => $instance->started_at,
                    'completed_at' => $instance->completed_at,
                ],
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update workflow instance variables
     */
    public function updateVariables(Request $request, string $instanceId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'variables' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $instance = $this->sharWorkflowService->getWorkflowInstance($instanceId);
            
            if (!$instance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow instance not found',
                ], 404);
            }

            $instance->updateVariables($request->input('variables'));

            return response()->json([
                'success' => true,
                'message' => 'Workflow instance variables updated successfully',
                'data' => [
                    'id' => $instance->id,
                    'variables' => $instance->fresh()->variables,
                ],
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}