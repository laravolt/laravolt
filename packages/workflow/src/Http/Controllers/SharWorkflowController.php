<?php

namespace Laravolt\Workflow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharWorkflowController extends Controller
{
    protected SharWorkflowService $sharWorkflowService;

    public function __construct(SharWorkflowService $sharWorkflowService)
    {
        $this->sharWorkflowService = $sharWorkflowService;
    }

    /**
     * Display a listing of workflows
     */
    public function index(): JsonResponse
    {
        try {
            $workflows = $this->sharWorkflowService->getWorkflows();
            
            return response()->json([
                'success' => true,
                'data' => $workflows->map(function ($workflow) {
                    return [
                        'id' => $workflow->id,
                        'name' => $workflow->name,
                        'description' => $workflow->description,
                        'version' => $workflow->version,
                        'status' => $workflow->status,
                        'created_at' => $workflow->created_at,
                        'statistics' => $workflow->getStatistics(),
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
     * Store a newly created workflow
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bpmn_xml' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $workflow = $this->sharWorkflowService->createWorkflow(
                $request->input('name'),
                $request->input('bpmn_xml'),
                $request->input('description'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Workflow created successfully',
                'data' => [
                    'id' => $workflow->id,
                    'name' => $workflow->name,
                    'description' => $workflow->description,
                    'version' => $workflow->version,
                    'status' => $workflow->status,
                    'created_at' => $workflow->created_at,
                ],
            ], 201);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified workflow
     */
    public function show(string $name): JsonResponse
    {
        try {
            $workflow = $this->sharWorkflowService->getWorkflow($name);
            
            if (!$workflow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $workflow->id,
                    'name' => $workflow->name,
                    'description' => $workflow->description,
                    'version' => $workflow->version,
                    'status' => $workflow->status,
                    'bpmn_xml' => $workflow->bpmn_xml,
                    'created_at' => $workflow->created_at,
                    'statistics' => $workflow->getStatistics(),
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
     * Remove the specified workflow
     */
    public function destroy(string $name): JsonResponse
    {
        try {
            $this->sharWorkflowService->deleteWorkflow($name);

            return response()->json([
                'success' => true,
                'message' => 'Workflow deleted successfully',
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Launch a workflow instance
     */
    public function launch(Request $request, string $name): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'variables' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $instance = $this->sharWorkflowService->launchWorkflowInstance(
                $name,
                $request->input('variables', []),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Workflow instance launched successfully',
                'data' => [
                    'id' => $instance->id,
                    'workflow_name' => $instance->workflow_name,
                    'status' => $instance->status,
                    'variables' => $instance->variables,
                    'started_at' => $instance->started_at,
                ],
            ], 201);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get workflow statistics
     */
    public function statistics(string $name = null): JsonResponse
    {
        try {
            $statistics = $this->sharWorkflowService->getWorkflowStatistics($name);

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (SharException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}