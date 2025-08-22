# SHAR Async Operations

This document explains how SHAR operations are handled asynchronously to prevent blocking web requests in Laravel applications.

## Overview

SHAR operations are inherently network-dependent and can take time to complete. To ensure a responsive user experience, all SHAR operations are processed asynchronously using Laravel's queue system.

## Architecture

```
┌─────────────────┐    HTTP Request    ┌─────────────────┐    Queue Job    ┌─────────────────┐
│   Web Request   │ ──────────────────> │   Controller    │ ──────────────> │   Queue Worker  │
│   (User/API)    │                     │   (Fast Return) │                 │   (Background)  │
└─────────────────┘                     └─────────────────┘                 └─────────────────┘
        │                                        │                                    │
        │ 202 Accepted                           │                                    │
        │ (Immediate Response)                   │                                    ▼
        ▼                                        ▼                          ┌─────────────────┐
┌─────────────────┐                     ┌─────────────────┐                │   SHAR Server   │
│   User Gets     │                     │   Database      │                │   (External)    │
│   Job Status    │                     │   (Placeholder) │ ◄──────────────┤                 │
└─────────────────┘                     └─────────────────┘                └─────────────────┘
```

## Queue Jobs

### 1. CreateSharWorkflowJob
Handles workflow creation in the background.

**Features:**
- Creates placeholder workflow immediately
- Processes SHAR creation asynchronously
- Updates workflow status upon completion
- Supports retry mechanism with exponential backoff
- Dispatches events for success/failure

### 2. LaunchSharWorkflowInstanceJob
Handles workflow instance launching in the background.

**Features:**
- Creates placeholder instance immediately
- Launches instance in SHAR asynchronously
- Updates instance status and ID upon completion
- Supports callback URLs for notifications

### 3. SyncSharWorkflowInstanceJob
Handles instance synchronization with SHAR.

**Features:**
- Syncs instance status and variables
- Handles completion detection
- Lightweight operation suitable for frequent execution

### 4. PeriodicSharSyncJob
Scheduled job for bulk synchronization.

**Features:**
- Syncs all running instances periodically
- Processes in batches to avoid overwhelming SHAR
- Configurable batch size and delay

## Usage

### Asynchronous Operations (Default)

#### Create Workflow
```php
// API Request
POST /api/shar/workflows
{
    "name": "OrderProcess",
    "bpmn_xml": "...",
    "async": true,
    "callback_url": "https://myapp.com/callbacks/workflow-created"
}

// Immediate Response (202 Accepted)
{
    "success": true,
    "message": "Workflow creation queued successfully",
    "data": {
        "id": 123,
        "name": "OrderProcess",
        "status": "creating",
        "async": true
    }
}
```

#### Launch Instance
```php
// API Request
POST /api/shar/workflows/OrderProcess/launch
{
    "variables": {"orderId": "12345"},
    "async": true,
    "callback_url": "https://myapp.com/callbacks/instance-launched"
}

// Immediate Response (202 Accepted)
{
    "success": true,
    "message": "Workflow instance launch queued successfully",
    "data": {
        "id": "uuid-instance-id",
        "workflow_name": "OrderProcess",
        "status": "launching",
        "async": true
    }
}
```

### Synchronous Operations (Blocking)

For cases where you need immediate results:

```php
// Force synchronous operation
POST /api/shar/workflows
{
    "name": "OrderProcess",
    "bpmn_xml": "...",
    "async": false
}

// Response after SHAR completes (201 Created)
{
    "success": true,
    "message": "Workflow created successfully",
    "data": {
        "id": 123,
        "name": "OrderProcess",
        "status": "active",
        "async": false
    }
}
```

### Programmatic Usage

#### Using Async Service
```php
use Laravolt\Workflow\SharAsyncWorkflowService;

$asyncService = app(SharAsyncWorkflowService::class);

// Create workflow asynchronously
$requestId = $asyncService->createWorkflowAsync(
    name: 'OrderProcess',
    bpmnXml: $bpmnContent,
    description: 'Order processing workflow',
    createdBy: auth()->id(),
    callbackUrl: 'https://myapp.com/callbacks/workflow-created'
);

// Launch instance asynchronously
$requestId = $asyncService->launchWorkflowInstanceAsync(
    workflowName: 'OrderProcess',
    variables: ['orderId' => '12345'],
    createdBy: auth()->id(),
    callbackUrl: 'https://myapp.com/callbacks/instance-launched'
);

// Sync instance asynchronously
$asyncService->syncWorkflowInstanceAsync($instanceId);

// Batch sync all running instances
$syncedCount = $asyncService->syncAllRunningInstancesAsync();
```

#### Using Placeholder Methods
```php
// Create workflow with immediate placeholder
$workflow = $asyncService->createWorkflowWithPlaceholder(
    'OrderProcess',
    $bpmnContent,
    'Order processing workflow',
    auth()->id()
);

// Returns immediately with status 'creating'
// Background job will update status to 'active' when complete

// Launch instance with immediate placeholder
$instance = $asyncService->launchInstanceWithPlaceholder(
    'OrderProcess',
    ['orderId' => '12345'],
    auth()->id()
);

// Returns immediately with status 'launching'
// Background job will update status to 'running' when launched
```

## Status Flow

### Workflow Status Flow
```
creating → active (success)
creating → failed (error)
```

### Instance Status Flow
```
launching → running (success)
launching → failed (error)
running → completed (success)
running → failed (error)
```

## Events

The async system dispatches events for workflow lifecycle management:

### Workflow Events
```php
// Listen for workflow creation
Event::listen(SharWorkflowCreated::class, function ($event) {
    $workflow = $event->workflow;
    $sharResponse = $event->sharResponse;
    
    // Handle successful workflow creation
    Log::info("Workflow {$workflow->name} created successfully");
});

// Listen for workflow creation failures
Event::listen(SharWorkflowCreationFailed::class, function ($event) {
    $workflowName = $event->workflowName;
    $error = $event->errorMessage;
    
    // Handle workflow creation failure
    Log::error("Failed to create workflow {$workflowName}: {$error}");
});
```

### Instance Events
```php
// Listen for instance launches
Event::listen(SharWorkflowInstanceLaunched::class, function ($event) {
    $instance = $event->instance;
    
    // Handle successful instance launch
    Log::info("Instance {$instance->id} launched successfully");
});

// Listen for instance completion
Event::listen(SharWorkflowInstanceCompleted::class, function ($event) {
    $instance = $event->instance;
    
    // Handle instance completion
    // This automatically triggers notifications
    Log::info("Instance {$instance->id} completed");
});

// Listen for instance sync
Event::listen(SharWorkflowInstanceSynced::class, function ($event) {
    $instance = $event->instance;
    $previousStatus = $event->previousStatus;
    $newStatus = $event->newStatus;
    
    if ($previousStatus !== $newStatus) {
        Log::info("Instance {$instance->id} status changed: {$previousStatus} → {$newStatus}");
    }
});
```

## Notifications

### Automatic Notifications
The system automatically sends notifications when workflow instances are completed:

```php
// Users receive email and database notifications
// when their workflow instances complete
```

### Custom Notifications
```php
use Laravolt\Workflow\Notifications\SharWorkflowCompletedNotification;

// Send custom notification
$user->notify(new SharWorkflowCompletedNotification($instance));
```

## Queue Management

### Queue Workers

Start queue workers for SHAR operations:

```bash
# Start all SHAR queue workers
php artisan shar:queue start

# Start specific queue worker
php artisan shar:queue start --queue=shar-instances

# Check queue status
php artisan shar:queue status

# Trigger manual sync
php artisan shar:queue sync

# Clear failed jobs
php artisan shar:queue clear
```

### Production Setup

#### Supervisor Configuration
```ini
[program:shar-workflows]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --queue=shar-workflows --sleep=3 --tries=3 --timeout=300
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/shar-workflows.log

[program:shar-instances]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --queue=shar-instances --sleep=3 --tries=3 --timeout=300
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/shar-instances.log

[program:shar-sync]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --queue=shar-sync --sleep=3 --tries=2 --timeout=120
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/shar-sync.log
```

#### Scheduled Jobs
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Periodic sync every 5 minutes
    $schedule->job(new PeriodicSharSyncJob())
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->onOneServer();
        
    // Cleanup old instances daily
    $schedule->command('shar:cleanup --days=30 --status=completed --force')
        ->daily()
        ->at('02:00');
}
```

## Configuration

### Environment Variables
```env
# Enable async operations (default: true)
SHAR_ASYNC_ENABLED=true

# Queue names
SHAR_WORKFLOW_QUEUE=shar-workflows
SHAR_INSTANCE_QUEUE=shar-instances  
SHAR_SYNC_QUEUE=shar-sync

# Sync interval in seconds (default: 300 = 5 minutes)
SHAR_SYNC_INTERVAL=300

# Enable notifications (default: true)
SHAR_NOTIFICATIONS_ENABLED=true
```

### Queue Configuration
Ensure your `config/queue.php` includes the SHAR queues:

```php
'connections' => [
    'redis' => [
        // ... existing config
        'queues' => [
            'default',
            'shar-workflows',
            'shar-instances',
            'shar-sync',
        ],
    ],
],
```

## Monitoring

### Queue Monitoring
```bash
# Monitor queue status
php artisan queue:monitor shar-workflows,shar-instances,shar-sync

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Instance Status Tracking
```php
// Check if workflow is still being created
$workflow = SharWorkflow::find($id);
if ($workflow->status === 'creating') {
    // Still being processed
}

// Check if instance is still being launched
$instance = SharWorkflowInstance::find($id);
if ($instance->status === 'launching') {
    // Still being processed
}
```

## Error Handling

### Retry Logic
- **Workflow Creation**: 3 attempts with exponential backoff (10s, 30s, 60s)
- **Instance Launch**: 3 attempts with quick backoff (5s, 15s, 30s)
- **Instance Sync**: 2 attempts with short backoff (5s, 15s)

### Failure Handling
```php
// Listen for permanent failures
Event::listen(SharWorkflowCreationFailed::class, function ($event) {
    // Notify administrators
    // Update workflow status to 'failed'
    // Send user notification
});

Event::listen(SharWorkflowInstanceLaunchFailed::class, function ($event) {
    // Log failure details
    // Notify user of launch failure
    // Optionally retry with different parameters
});
```

## Callback URLs

### Webhook Support
SHAR jobs can send HTTP callbacks when operations complete:

```php
// Register callback URL when creating workflow
$asyncService->createWorkflowAsync(
    'OrderProcess',
    $bpmnXml,
    'Description',
    auth()->id(),
    'https://myapp.com/webhooks/shar/workflow-created'
);

// Callback payload example
{
    "job_type": "create_workflow",
    "workflow_name": "OrderProcess",
    "status": "success",
    "data": {
        "workflow": {...},
        "shar_response": {...}
    },
    "timestamp": "2024-01-01T12:00:00Z"
}
```

### Handling Callbacks
```php
// In your webhook controller
Route::post('/webhooks/shar/workflow-created', function (Request $request) {
    $payload = $request->json()->all();
    
    if ($payload['status'] === 'success') {
        // Handle successful workflow creation
        $workflowData = $payload['data']['workflow'];
        
        // Update UI, send notifications, etc.
    } else {
        // Handle failure
        $error = $payload['data']['error'];
        
        // Log error, notify user, etc.
    }
    
    return response()->json(['received' => true]);
});
```

## Best Practices

### 1. Always Use Async for User-Facing Operations
```php
// ✅ Good - Non-blocking
$workflow = $asyncService->createWorkflowWithPlaceholder(...);
return response()->json(['status' => 'queued', 'id' => $workflow->id], 202);

// ❌ Bad - Blocks user request
$workflow = $syncService->createWorkflow(...); // Can take 5-30 seconds
return response()->json(['workflow' => $workflow], 201);
```

### 2. Provide Status Endpoints
```php
// Let users check operation status
Route::get('/api/shar/workflows/{id}/status', function ($id) {
    $workflow = SharWorkflow::find($id);
    return response()->json([
        'status' => $workflow->status,
        'ready' => $workflow->status === 'active',
    ]);
});
```

### 3. Use Events for Integration
```php
// React to workflow events
Event::listen(SharWorkflowCreated::class, function ($event) {
    // Update caches
    // Send notifications
    // Update external systems
});
```

### 4. Monitor Queue Health
```bash
# Add to monitoring scripts
php artisan shar:queue status --format=json
php artisan queue:monitor shar-workflows,shar-instances,shar-sync
```

### 5. Configure Appropriate Timeouts
```php
// In queue job
public $timeout = 300; // 5 minutes for workflow operations
public $timeout = 120; // 2 minutes for sync operations
```

## Troubleshooting

### Common Issues

1. **Jobs Not Processing**
   ```bash
   # Check if queue workers are running
   php artisan shar:queue status
   
   # Start workers if needed
   php artisan shar:queue start
   ```

2. **High Queue Backlog**
   ```bash
   # Check queue size
   php artisan queue:monitor shar-workflows,shar-instances,shar-sync
   
   # Scale up workers or optimize job processing
   ```

3. **Failed Jobs Accumulating**
   ```bash
   # Check failed jobs
   php artisan queue:failed
   
   # Retry specific jobs
   php artisan queue:retry {job-id}
   
   # Clear old failed jobs
   php artisan queue:flush
   ```

4. **SHAR Server Connectivity Issues**
   ```bash
   # Check SHAR health
   php artisan shar:health
   
   # Manual sync to catch up
   php artisan shar:sync --status=running --force
   ```

### Debugging

#### Enable Debug Logging
```env
LOG_LEVEL=debug
SHAR_LOG_LEVEL=debug
```

#### Monitor Job Processing
```bash
# Watch queue in real-time
php artisan queue:work --verbose

# Monitor specific queue
php artisan queue:work --queue=shar-instances --verbose
```

#### Check Job Status
```php
// In your application
use Illuminate\Support\Facades\Queue;

// Monitor job processing
Queue::after(function (JobProcessed $event) {
    if (str_contains($event->job->resolveName(), 'Shar')) {
        Log::info('SHAR job completed', [
            'job' => $event->job->resolveName(),
            'queue' => $event->job->getQueue(),
        ]);
    }
});
```

## Performance Optimization

### 1. Queue Separation
- **shar-workflows**: High priority, low volume
- **shar-instances**: Medium priority, high volume  
- **shar-sync**: Low priority, very high volume

### 2. Worker Scaling
```bash
# Scale workers based on load
# Workflows: 1-2 workers (CPU intensive)
# Instances: 2-4 workers (Network I/O)
# Sync: 2-3 workers (Lightweight)
```

### 3. Batch Processing
```php
// Process multiple operations together
$asyncService->batchSyncInstancesAsync($instanceIds, 'shar-sync', 1);
```

### 4. Rate Limiting
```php
// In job classes
public $tries = 3;
public $backoff = [10, 30, 60]; // Exponential backoff
```

## Integration Examples

### React Frontend
```javascript
// Create workflow
const response = await fetch('/api/shar/workflows', {
    method: 'POST',
    body: JSON.stringify({
        name: 'OrderProcess',
        bpmn_xml: bpmnContent,
        async: true
    })
});

if (response.status === 202) {
    const data = await response.json();
    
    // Poll for completion
    const checkStatus = async () => {
        const statusResponse = await fetch(`/api/shar/workflows/${data.data.name}`);
        const status = await statusResponse.json();
        
        if (status.data.status === 'active') {
            // Workflow is ready
            showSuccess('Workflow created successfully!');
        } else if (status.data.status === 'failed') {
            // Creation failed
            showError('Workflow creation failed');
        } else {
            // Still processing
            setTimeout(checkStatus, 2000);
        }
    };
    
    checkStatus();
}
```

### CLI Integration
```bash
# Create workflow and wait for completion
WORKFLOW_ID=$(php artisan shar:workflow:create OrderProcess workflow.bpmn --format=json | jq -r '.id')

# Poll until ready
while true; do
    STATUS=$(php artisan shar:workflow:list --format=json | jq -r ".[] | select(.name==\"OrderProcess\") | .status")
    
    if [ "$STATUS" = "active" ]; then
        echo "Workflow is ready!"
        break
    elif [ "$STATUS" = "failed" ]; then
        echo "Workflow creation failed!"
        exit 1
    else
        echo "Waiting for workflow creation..."
        sleep 5
    fi
done
```

This async implementation ensures that SHAR operations never block user requests while providing comprehensive monitoring, error handling, and notification capabilities.