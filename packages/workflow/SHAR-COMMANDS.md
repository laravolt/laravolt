# SHAR Artisan Commands

This document provides a comprehensive guide to all available SHAR (Simple Hyperscale Activity Router) Artisan commands for managing BPMN workflows in Laravolt.

## Quick Reference

```bash
# Get help for all SHAR commands
php artisan shar:help

# Setup SHAR integration
php artisan shar:setup --example

# Check health and connectivity
php artisan shar:health

# Monitor workflows in real-time
php artisan shar:monitor
```

## Command Categories

### 🛠️ Setup & Configuration

#### `shar:setup`
Setup and configure SHAR integration with automatic checks and configuration.

```bash
# Basic setup
php artisan shar:setup

# Setup with example workflow
php artisan shar:setup --example

# Skip migrations and Docker checks
php artisan shar:setup --skip-migrations --skip-docker
```

**What it does:**
- Checks configuration settings
- Runs database migrations
- Verifies Docker setup
- Tests SHAR connectivity
- Optionally creates example workflow

#### `shar:health`
Check SHAR server health and connectivity status.

```bash
# Single health check
php artisan shar:health

# Continuous monitoring
php artisan shar:health --watch --interval=10

# JSON output for scripts
php artisan shar:health --format=json
```

### 📊 Monitoring & Statistics

#### `shar:monitor`
Real-time monitoring dashboard for workflow instances.

```bash
# Monitor all workflows
php artisan shar:monitor

# Monitor specific workflow
php artisan shar:monitor --workflow=OrderProcess

# Auto-sync with custom interval
php artisan shar:monitor --auto-sync --interval=5 --limit=50
```

**Features:**
- Real-time instance status updates
- Health monitoring
- Statistics with change indicators
- Auto-sync capability

#### `shar:statistics`
Display comprehensive workflow statistics and analytics.

```bash
# Global statistics
php artisan shar:statistics

# Detailed breakdown
php artisan shar:statistics --detailed

# Specific workflow stats
php artisan shar:statistics --workflow=OrderProcess

# JSON format for integration
php artisan shar:statistics --format=json
```

### 📝 Workflow Management

#### `shar:workflow:create`
Create new workflows from BPMN files.

```bash
# Basic workflow creation
php artisan shar:workflow:create OrderProcess /path/to/order.bpmn

# With description and user
php artisan shar:workflow:create OrderProcess order.bpmn \
  --description="Order processing workflow" \
  --user-id=1
```

#### `shar:workflow:list`
List all available workflows with filtering options.

```bash
# List all workflows
php artisan shar:workflow:list

# Filter by status
php artisan shar:workflow:list --status=active

# JSON output
php artisan shar:workflow:list --format=json
```

#### `shar:workflow:launch`
Launch workflow instances with variables and monitoring.

```bash
# Simple launch
php artisan shar:workflow:launch OrderProcess

# With variables
php artisan shar:workflow:launch OrderProcess \
  --variables='{"orderId":"12345","amount":99.99}'

# Wait for completion
php artisan shar:workflow:launch OrderProcess --wait

# Launch with specific user
php artisan shar:workflow:launch OrderProcess --user-id=1
```

#### `shar:workflow:delete`
Delete workflows with safety checks.

```bash
# Delete with confirmation
php artisan shar:workflow:delete OrderProcess

# Force delete without confirmation
php artisan shar:workflow:delete OrderProcess --force
```

#### `shar:workflow:export`
Export workflows to BPMN files.

```bash
# Export to default filename
php artisan shar:workflow:export OrderProcess

# Custom output path
php artisan shar:workflow:export OrderProcess --output=/path/to/export.bpmn

# Export as JSON with metadata
php artisan shar:workflow:export OrderProcess --format=json --output=workflow.json
```

### 🔄 Instance Management

#### `shar:instance:list`
List workflow instances with filtering and pagination.

```bash
# List all instances
php artisan shar:instance:list

# Filter by workflow
php artisan shar:instance:list --workflow=OrderProcess

# Filter by status with limit
php artisan shar:instance:list --status=running --limit=50

# JSON output
php artisan shar:instance:list --format=json
```

#### `shar:instance:show`
Show detailed information about specific instances.

```bash
# Show instance details
php artisan shar:instance:show abc123def456

# Sync before showing
php artisan shar:instance:show abc123def456 --sync

# JSON output
php artisan shar:instance:show abc123def456 --format=json
```

#### `shar:instance:complete`
Complete workflow instances manually.

```bash
# Complete with confirmation
php artisan shar:instance:complete abc123def456

# Force complete without confirmation
php artisan shar:instance:complete abc123def456 --force
```

### 🔧 Maintenance & Operations

#### `shar:sync`
Synchronize workflow instances with SHAR server.

```bash
# Sync single instance
php artisan shar:sync --instance=abc123def456

# Sync all instances of a workflow
php artisan shar:sync --workflow=OrderProcess

# Sync running instances
php artisan shar:sync --status=running --batch-size=100

# Force sync without confirmation
php artisan shar:sync --workflow=OrderProcess --force
```

#### `shar:cleanup`
Clean up old workflow instances to free database space.

```bash
# Dry run to see what would be deleted
php artisan shar:cleanup --days=30 --dry-run

# Clean up completed instances older than 90 days
php artisan shar:cleanup --days=90 --status=completed

# Force cleanup without confirmation
php artisan shar:cleanup --days=30 --force --batch-size=200
```

#### `shar:batch`
Perform batch operations on workflows and instances.

```bash
# Import workflows from JSON file
php artisan shar:batch import --file=workflows.json --dry-run

# Launch 100 instances of a workflow
php artisan shar:batch launch --workflow=OrderProcess --count=100 \
  --variables='{"environment":"production"}'

# Complete multiple instances
php artisan shar:batch complete --instances=id1,id2,id3,id4 --force
```

## Advanced Usage Examples

### 1. Complete Workflow Deployment

```bash
# 1. Setup SHAR integration
php artisan shar:setup --example

# 2. Create your workflow
php artisan shar:workflow:create MyWorkflow workflow.bpmn \
  --description="My production workflow"

# 3. Test with a single instance
php artisan shar:workflow:launch MyWorkflow \
  --variables='{"test":true}' --wait

# 4. Monitor the workflow
php artisan shar:monitor --workflow=MyWorkflow
```

### 2. Production Monitoring Setup

```bash
# Health monitoring (add to cron)
*/5 * * * * php artisan shar:health --format=json >> /var/log/shar-health.log

# Daily cleanup (add to cron)
0 2 * * * php artisan shar:cleanup --days=30 --status=completed --force

# Weekly sync (add to cron)
0 3 * * 0 php artisan shar:sync --status=running --force
```

### 3. Batch Operations

```bash
# Create JSON file for batch import
cat > workflows.json << EOF
[
  {
    "name": "Workflow1",
    "bpmn_xml": "<?xml version=\"1.0\"...",
    "description": "First workflow"
  },
  {
    "name": "Workflow2", 
    "bpmn_xml": "<?xml version=\"1.0\"...",
    "description": "Second workflow"
  }
]
EOF

# Import all workflows
php artisan shar:batch import --file=workflows.json

# Launch multiple instances for load testing
php artisan shar:batch launch --workflow=LoadTest --count=1000 \
  --variables='{"loadTest":true}'
```

### 4. Debugging and Troubleshooting

```bash
# Check overall system health
php artisan shar:health --format=json

# Monitor specific workflow with auto-sync
php artisan shar:monitor --workflow=ProblematicWorkflow --auto-sync

# Show detailed instance information
php artisan shar:instance:show abc123 --sync --format=json

# Sync all running instances to get latest status
php artisan shar:sync --status=running --force
```

## JSON Output Formats

All commands support `--format=json` for programmatic usage:

### Health Check JSON
```json
{
  "health": {
    "status": "healthy",
    "timestamp": "2024-01-01T12:00:00Z"
  },
  "statistics": {
    "total_instances": 150,
    "running_instances": 5,
    "completed_instances": 140,
    "failed_instances": 5
  },
  "configuration": {
    "base_url": "http://localhost:8080",
    "timeout": 30,
    "enabled": true
  }
}
```

### Instance Details JSON
```json
{
  "id": "abc123def456",
  "workflow_name": "OrderProcess",
  "status": "running",
  "variables": {
    "orderId": "12345",
    "amount": 99.99
  },
  "started_at": "2024-01-01T10:00:00Z",
  "completed_at": null,
  "duration_seconds": null,
  "tracking_code": "abc123def456"
}
```

## Error Handling

All commands include comprehensive error handling:

- **Connection Errors**: Graceful handling when SHAR server is unavailable
- **Validation Errors**: Clear messages for invalid inputs
- **Batch Processing**: Continue processing even if individual items fail
- **Logging**: Detailed error logs for debugging

## Integration with CI/CD

### Health Checks in CI
```yaml
# GitHub Actions example
- name: Check SHAR Health
  run: |
    php artisan shar:health --format=json
    if [ $? -ne 0 ]; then
      echo "SHAR health check failed"
      exit 1
    fi
```

### Automated Deployment
```bash
#!/bin/bash
# deployment-script.sh

# Deploy new workflow
php artisan shar:workflow:create ProductionWorkflow workflow.bpmn \
  --description="Production deployment $(date)"

# Verify deployment
php artisan shar:health

# Launch test instance
php artisan shar:workflow:launch ProductionWorkflow \
  --variables='{"environment":"production","version":"1.0"}' \
  --wait
```

## Best Practices

1. **Always use `--dry-run`** before batch operations
2. **Monitor health regularly** in production
3. **Clean up old instances** to maintain performance
4. **Use JSON format** for automation and logging
5. **Sync instances periodically** to ensure data consistency
6. **Export workflows** before major changes for backup

## Troubleshooting

### Common Issues

1. **SHAR server not responding**
   ```bash
   php artisan shar:health
   # Check Docker services: cd packages/shar && docker-compose ps
   ```

2. **Instance sync issues**
   ```bash
   php artisan shar:sync --instance=INSTANCE_ID --force
   ```

3. **Performance issues**
   ```bash
   # Clean up old instances
   php artisan shar:cleanup --days=7 --dry-run
   
   # Check statistics
   php artisan shar:statistics --detailed
   ```

4. **Configuration problems**
   ```bash
   php artisan shar:setup
   ```

For more detailed troubleshooting, see the main documentation files:
- `packages/shar/README.md` - Go service documentation
- `packages/workflow/README-SHAR.md` - Laravel integration guide