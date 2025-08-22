#!/bin/bash

set -e

echo "🚀 Setting up SHAR integration with Laravolt..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}$1${NC}"
}

# Check if required tools are installed
check_requirements() {
    print_header "Checking requirements..."
    
    if ! command -v docker &> /dev/null; then
        print_error "Docker is required but not installed."
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose is required but not installed."
        exit 1
    fi
    
    if ! command -v php &> /dev/null; then
        print_error "PHP is required but not installed."
        exit 1
    fi
    
    print_status "All requirements satisfied ✓"
}

# Setup environment variables
setup_env() {
    print_header "Setting up environment variables..."
    
    ENV_FILE=".env"
    
    if [ ! -f "$ENV_FILE" ]; then
        print_warning ".env file not found. Creating from .env.example..."
        if [ -f ".env.example" ]; then
            cp .env.example .env
        else
            touch .env
        fi
    fi
    
    # Add SHAR configuration if not exists
    if ! grep -q "SHAR_ENABLED" .env; then
        print_status "Adding SHAR configuration to .env..."
        cat >> .env << EOF

# SHAR Configuration
SHAR_ENABLED=true
SHAR_BASE_URL=http://localhost:8080
SHAR_TIMEOUT=30
NATS_URL=nats://localhost:4222
SHAR_LOG_LEVEL=info
EOF
    else
        print_status "SHAR configuration already exists in .env"
    fi
}

# Build and start SHAR services
start_shar() {
    print_header "Starting SHAR services..."
    
    cd packages/shar
    
    print_status "Building SHAR Docker image..."
    docker-compose build
    
    print_status "Starting NATS and SHAR services..."
    docker-compose up -d
    
    print_status "Waiting for services to be ready..."
    sleep 10
    
    # Check if services are running
    if curl -s http://localhost:8080/health > /dev/null; then
        print_status "SHAR server is running ✓"
    else
        print_warning "SHAR server may not be ready yet. Check with: curl http://localhost:8080/health"
    fi
    
    if curl -s http://localhost:8222/healthz > /dev/null; then
        print_status "NATS server is running ✓"
    else
        print_warning "NATS server may not be ready yet. Check with: curl http://localhost:8222/healthz"
    fi
    
    cd ../..
}

# Run Laravel migrations
run_migrations() {
    print_header "Running database migrations..."
    
    if command -v php artisan &> /dev/null; then
        php artisan migrate --force
        print_status "Migrations completed ✓"
    else
        print_warning "Laravel not found. Please run 'php artisan migrate' manually."
    fi
}

# Install Composer dependencies
install_dependencies() {
    print_header "Installing PHP dependencies..."
    
    # Check if guzzlehttp/guzzle is installed
    if ! composer show guzzlehttp/guzzle &> /dev/null; then
        print_status "Installing Guzzle HTTP client..."
        composer require guzzlehttp/guzzle
    else
        print_status "Guzzle HTTP client already installed ✓"
    fi
}

# Create example workflow
create_example() {
    print_header "Creating example workflow..."
    
    if [ -f "packages/shar/examples/simple-workflow.bpmn" ]; then
        print_status "Example BPMN file available at: packages/shar/examples/simple-workflow.bpmn"
        print_status "You can upload this through the web interface at /workflow/shar/workflows/create"
    else
        print_warning "Example BPMN file not found."
    fi
}

# Display completion message
show_completion() {
    print_header "🎉 SHAR integration setup completed!"
    
    echo ""
    echo "Next steps:"
    echo "1. 🚀 Start queue workers: php artisan shar:queue start"
    echo "2. 📊 Access the SHAR dashboard: http://localhost/workflow/shar"
    echo "3. 📝 Create your first workflow: http://localhost/workflow/shar/workflows/create"
    echo "4. 🔍 Monitor workflows: php artisan shar:monitor"
    echo "5. 🏥 Check health: php artisan shar:health"
    echo ""
    echo "API endpoints (async by default):"
    echo "- Workflows: http://localhost/api/shar/workflows"
    echo "- Instances: http://localhost/api/shar/instances"
    echo ""
    echo "Queue management:"
    echo "- Start workers: php artisan shar:queue start"
    echo "- Check status: php artisan shar:queue status"
    echo "- Monitor queues: php artisan queue:monitor shar-workflows,shar-instances,shar-sync"
    echo ""
    echo "Documentation:"
    echo "- SHAR Go service: packages/shar/README.md"
    echo "- Laravel integration: packages/workflow/README-SHAR.md"
    echo "- Async operations: packages/workflow/ASYNC-OPERATIONS.md"
    echo "- Command reference: packages/workflow/SHAR-COMMANDS.md"
    echo ""
    echo "To stop SHAR services: cd packages/shar && docker-compose down"
}

# Main execution
main() {
    print_header "🔧 SHAR Integration Setup"
    echo ""
    
    check_requirements
    setup_env
    install_dependencies
    start_shar
    run_migrations
    create_example
    show_completion
}

# Run main function
main "$@"