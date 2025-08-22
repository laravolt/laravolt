# Laravolt SHAR Integration

This document describes how to use SHAR (Simple Hyperscale Activity Router) with Laravolt for BPMN workflow management.

## Overview

The Laravolt workflow package now supports SHAR as an alternative workflow engine to Camunda. SHAR provides:

- Lightweight Go-based workflow execution
- NATS messaging for high performance
- BPMN 2.0 compatibility
- Horizontal scalability

## Installation

### 1. Enable SHAR in Configuration

Add to your `.env` file:

```env
SHAR_ENABLED=true
SHAR_BASE_URL=http://localhost:8080
SHAR_TIMEOUT=30
NATS_URL=nats://localhost:4222
SHAR_LOG_LEVEL=info
```

### 2. Run Database Migrations

```bash
php artisan migrate
```

This creates the following tables:
- `wf_shar_workflows` - Stores BPMN workflow definitions
- `wf_shar_workflow_instances` - Stores workflow execution instances

### 3. Start SHAR Server

Navigate to the SHAR package and start the services:

```bash
cd packages/shar
docker-compose up -d
```

## Usage

### Web Interface

Access the SHAR workflow management interface at:
- Dashboard: `/workflow/shar`
- Workflows: `/workflow/shar/workflows`
- Instances: `/workflow/shar/instances`

### API Endpoints

#### Workflows
- `GET /api/shar/workflows` - List workflows
- `POST /api/shar/workflows` - Create workflow
- `GET /api/shar/workflows/{name}` - Get workflow
- `DELETE /api/shar/workflows/{name}` - Delete workflow
- `POST /api/shar/workflows/{name}/launch` - Launch instance

#### Instances
- `GET /api/shar/instances` - List instances
- `GET /api/shar/instances/{id}` - Get instance
- `POST /api/shar/instances/{id}/complete` - Complete instance
- `POST /api/shar/instances/{id}/sync` - Sync with SHAR
- `PATCH /api/shar/instances/{id}/variables` - Update variables

### Programmatic Usage

#### Using the Service

```php
use Laravolt\Workflow\SharWorkflowService;

$sharService = app(SharWorkflowService::class);

// Create a workflow
$workflow = $sharService->createWorkflow(
    name: 'OrderProcessing',
    bpmnXml: file_get_contents('order-process.bpmn'),
    description: 'Order processing workflow',
    createdBy: auth()->id()
);

// Launch an instance
$instance = $sharService->launchWorkflowInstance(
    workflowName: 'OrderProcessing',
    variables: [
        'orderId' => '12345',
        'customerId' => '67890',
        'amount' => 299.99
    ],
    createdBy: auth()->id()
);

// Get workflow statistics
$stats = $sharService->getWorkflowStatistics('OrderProcessing');
```

#### Using Models Directly

```php
use Laravolt\Workflow\Models\SharWorkflow;
use Laravolt\Workflow\Models\SharWorkflowInstance;

// Get all active workflows
$workflows = SharWorkflow::where('status', 'active')->get();

// Get running instances
$runningInstances = SharWorkflowInstance::where('status', 'running')->get();

// Get workflow with instances
$workflow = SharWorkflow::with('instances')->find($id);
```

## BPMN Workflow Creation

### 1. Create BPMN File

Use a BPMN modeler (like Camunda Modeler) to create your workflow:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL" 
                  xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" 
                  id="Definitions_1">
  <bpmn:process id="SimpleProcess" isExecutable="true">
    <bpmn:startEvent id="StartEvent_1"/>
    <bpmn:serviceTask id="ProcessOrder" name="Process Order"/>
    <bpmn:endEvent id="EndEvent_1"/>
    <bpmn:sequenceFlow id="Flow_1" sourceRef="StartEvent_1" targetRef="ProcessOrder"/>
    <bpmn:sequenceFlow id="Flow_2" sourceRef="ProcessOrder" targetRef="EndEvent_1"/>
  </bpmn:process>
</bpmn:definitions>
```

### 2. Upload via Web Interface

1. Go to `/workflow/shar/workflows/create`
2. Enter workflow name and description
3. Upload your BPMN file
4. Click "Create Workflow"

### 3. Upload via API

```bash
curl -X POST http://your-app/api/shar/workflows \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "OrderProcessing",
    "bpmn_xml": "<?xml version=\"1.0\"...",
    "description": "Order processing workflow"
  }'
```

## Service Tasks

SHAR workflows can include service tasks that are executed by your Laravel application. Register service task handlers:

```php
// In a service provider or controller
use Laravolt\Workflow\SharWorkflowService;

$sharService = app(SharWorkflowService::class);

// This would typically be done through NATS subscriptions
// Implementation depends on your specific service task requirements
```

## Monitoring and Debugging

### Health Check

Check if SHAR server is running:

```php
$sharService = app(SharWorkflowService::class);
$health = $sharService->checkHealth();

if ($health['status'] === 'healthy') {
    // SHAR is running
} else {
    // SHAR is down or unreachable
}
```

### Workflow Statistics

```php
// Global statistics
$globalStats = $sharService->getWorkflowStatistics();

// Workflow-specific statistics
$workflowStats = $sharService->getWorkflowStatistics('OrderProcessing');

// Example output:
[
    'total_instances' => 150,
    'running_instances' => 5,
    'completed_instances' => 140,
    'failed_instances' => 5,
    'average_duration' => 45.2 // seconds
]
```

### Instance Tracking

```php
// Get instance details
$instance = $sharService->getWorkflowInstance($instanceId);

// Sync with SHAR (get latest status)
$instance = $sharService->syncWorkflowInstance($instanceId);

// Check instance status
if ($instance->isRunning()) {
    // Instance is still executing
} elseif ($instance->isCompleted()) {
    // Instance finished successfully
} elseif ($instance->hasFailed()) {
    // Instance failed
}
```

## Events

The SHAR integration dispatches Laravel events for workflow lifecycle:

```php
// Listen for workflow events
Event::listen(WorkflowCreated::class, function ($event) {
    // Handle workflow creation
});

Event::listen(WorkflowInstanceLaunched::class, function ($event) {
    // Handle instance launch
});

Event::listen(WorkflowInstanceCompleted::class, function ($event) {
    // Handle instance completion
});
```

## Configuration Options

Update `config/workflow.php`:

```php
'shar' => [
    'base_url' => env('SHAR_BASE_URL', 'http://localhost:8080'),
    'timeout' => env('SHAR_TIMEOUT', 30),
    'enabled' => env('SHAR_ENABLED', false),
    'nats_url' => env('NATS_URL', 'nats://127.0.0.1:4222'),
    'log_level' => env('SHAR_LOG_LEVEL', 'info'),
],
```

## Troubleshooting

### Common Issues

1. **SHAR Server Not Responding**
   ```bash
   # Check if SHAR is running
   curl http://localhost:8080/health
   
   # Check Docker containers
   docker-compose ps
   ```

2. **NATS Connection Issues**
   ```bash
   # Check NATS health
   curl http://localhost:8222/healthz
   ```

3. **Workflow Creation Fails**
   - Validate BPMN XML syntax
   - Check Laravel logs for detailed errors
   - Verify SHAR server logs

4. **Database Issues**
   ```bash
   # Run migrations
   php artisan migrate
   
   # Check migration status
   php artisan migrate:status
   ```

### Debug Mode

Enable debug logging in both Laravel and SHAR:

```env
# Laravel
LOG_LEVEL=debug

# SHAR
SHAR_LOG_LEVEL=debug
```

## Performance Considerations

- **NATS Clustering**: For production, consider running NATS in cluster mode
- **SHAR Scaling**: Multiple SHAR instances can share the same NATS cluster
- **Database Indexing**: Ensure proper indexes on workflow instance queries
- **Caching**: Consider caching workflow definitions for frequently accessed workflows

## Security

- **Authentication**: All Laravel routes require authentication
- **CORS**: SHAR server includes CORS headers for web interface
- **Network Security**: Consider running SHAR and NATS in private networks
- **Environment Variables**: Store sensitive configuration in environment variables