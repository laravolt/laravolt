# SHAR - Simple Hyperscale Activity Router

SHAR is a lightweight, Go-based workflow engine designed to execute BPMN workflows at scale. This implementation provides HTTP API endpoints for integration with Laravel/Laravolt applications.

## Features

- **BPMN 2.0 Support**: Execute standard BPMN workflows
- **NATS Integration**: High-performance messaging with NATS
- **HTTP API**: RESTful endpoints for workflow management
- **Scalable**: Designed for high throughput and redundancy
- **Configurable Logging**: Multiple log levels (debug, info, warn, error)
- **Health Monitoring**: Built-in health check endpoints
- **Docker Support**: Container-ready with docker-compose

## Prerequisites

- Go 1.21 or higher
- NATS Server (2.10+)
- Docker and Docker Compose (for containerized deployment)

## Quick Start

### Using Docker Compose (Recommended)

1. **Clone and build**:
   ```bash
   cd packages/shar
   docker-compose up -d
   ```

2. **Verify services are running**:
   ```bash
   curl http://localhost:8080/health
   ```

### Manual Setup

1. **Start NATS Server**:
   ```bash
   docker run -p 4222:4222 -p 8222:8222 nats:2.10-alpine --jetstream
   ```

2. **Configure environment**:
   ```bash
   export NATS_URL=nats://localhost:4222
   export SHAR_LOG_LEVEL=info
   export SHAR_PORT=8080
   ```

3. **Build and run SHAR**:
   ```bash
   go mod tidy
   go build -o shar-server .
   ./shar-server
   ```

## Configuration

Configure SHAR using environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `NATS_URL` | NATS server URL | `nats://127.0.0.1:4222` |
| `SHAR_LOG_LEVEL` | Logging level (debug, info, warn, error) | `info` |
| `SHAR_PORT` | HTTP server port | `8080` |
| `SHAR_HOST` | HTTP server host | `0.0.0.0` |
| `SHAR_WORKFLOW_TIMEOUT` | Workflow timeout in seconds | `300` |

## API Endpoints

### Workflows

- `POST /api/v1/workflows` - Create a new workflow
- `GET /api/v1/workflows` - List all workflows
- `GET /api/v1/workflows/{name}` - Get specific workflow
- `DELETE /api/v1/workflows/{name}` - Delete workflow

### Workflow Instances

- `POST /api/v1/workflows/{name}/instances` - Launch workflow instance
- `GET /api/v1/instances` - List all instances
- `GET /api/v1/instances/{id}` - Get specific instance
- `POST /api/v1/instances/{id}/complete` - Complete instance

### Health Check

- `GET /health` - Server health status

## API Usage Examples

### Create a Workflow

```bash
curl -X POST http://localhost:8080/api/v1/workflows \
  -H "Content-Type: application/json" \
  -d '{
    "name": "SimpleProcess",
    "bpmn_xml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>..."
  }'
```

### Launch Workflow Instance

```bash
curl -X POST http://localhost:8080/api/v1/workflows/SimpleProcess/instances \
  -H "Content-Type: application/json" \
  -d '{
    "workflow_name": "SimpleProcess",
    "variables": {
      "userId": "123",
      "amount": 100.50
    }
  }'
```

### Check Health

```bash
curl http://localhost:8080/health
```

## Integration with Laravel/Laravolt

This SHAR service is designed to work with the Laravolt workflow package. The Laravel application communicates with SHAR through the HTTP API.

### Laravel Configuration

Add to your `.env` file:

```env
SHAR_ENABLED=true
SHAR_BASE_URL=http://localhost:8080
SHAR_TIMEOUT=30
NATS_URL=nats://localhost:4222
SHAR_LOG_LEVEL=info
```

### Laravel Usage

```php
use Laravolt\Workflow\SharWorkflowService;

// Inject the service
$sharService = app(SharWorkflowService::class);

// Create a workflow
$workflow = $sharService->createWorkflow(
    'MyWorkflow', 
    $bpmnXmlContent, 
    'Description', 
    auth()->id()
);

// Launch an instance
$instance = $sharService->launchWorkflowInstance(
    'MyWorkflow', 
    ['key' => 'value'], 
    auth()->id()
);
```

## Development

### Building

```bash
go mod tidy
go build -o shar-server .
```

### Testing

```bash
go test ./...
```

### Docker Build

```bash
docker build -t shar:latest .
```

## Architecture

```
┌─────────────────┐    HTTP API    ┌─────────────────┐    NATS    ┌─────────────────┐
│   Laravel App   │ ──────────────> │   SHAR Server   │ ────────> │   NATS Server   │
│   (Laravolt)    │                 │   (Go Service)  │           │                 │
└─────────────────┘                 └─────────────────┘           └─────────────────┘
        │                                    │
        │                                    │
        v                                    v
┌─────────────────┐                 ┌─────────────────┐
│   PostgreSQL    │                 │  BPMN Workflow  │
│   (Workflow     │                 │   Execution     │
│    Metadata)    │                 │                 │
└─────────────────┘                 └─────────────────┘
```

## Monitoring

### Health Check

The `/health` endpoint provides server status:

```json
{
  "status": "healthy",
  "timestamp": "2024-01-01T12:00:00Z",
  "version": "1.0.0"
}
```

### Logs

SHAR provides structured logging with configurable levels. Logs include:
- HTTP request/response details
- Workflow creation and execution events
- NATS connection status
- Error details

## Production Deployment

### Docker Compose Production

```yaml
version: '3.8'
services:
  nats:
    image: nats:2.10-alpine
    restart: always
    volumes:
      - nats_data:/data
    environment:
      - NATS_CLUSTER_NAME=shar-cluster

  shar:
    image: shar:latest
    restart: always
    environment:
      - NATS_URL=nats://nats:4222
      - SHAR_LOG_LEVEL=warn
    depends_on:
      - nats
```

### Environment Variables for Production

```env
NATS_URL=nats://your-nats-cluster:4222
SHAR_LOG_LEVEL=warn
SHAR_PORT=8080
SHAR_HOST=0.0.0.0
SHAR_WORKFLOW_TIMEOUT=600
```

## Troubleshooting

### Common Issues

1. **NATS Connection Failed**
   - Verify NATS server is running
   - Check `NATS_URL` configuration
   - Ensure network connectivity

2. **Workflow Creation Failed**
   - Validate BPMN XML syntax
   - Check SHAR server logs
   - Verify NATS connectivity

3. **Instance Launch Failed**
   - Ensure workflow exists in SHAR
   - Check variable format
   - Review workflow definition

### Debugging

Enable debug logging:
```bash
export SHAR_LOG_LEVEL=debug
```

Check NATS connectivity:
```bash
curl http://localhost:8222/healthz  # NATS health
curl http://localhost:8080/health   # SHAR health
```

## License

MIT License