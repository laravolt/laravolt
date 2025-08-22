.PHONY: build run test clean docker-build docker-run deps help

# Default target
help:
	@echo "Available targets:"
	@echo "  build        - Build the SHAR server binary"
	@echo "  run          - Run the SHAR server"
	@echo "  test         - Run tests"
	@echo "  clean        - Clean build artifacts"
	@echo "  docker-build - Build Docker image"
	@echo "  docker-run   - Run with Docker Compose"
	@echo "  docker-stop  - Stop Docker Compose services"
	@echo "  deps         - Download dependencies"
	@echo "  fmt          - Format code"
	@echo "  lint         - Run linter"

# Build the server
build: deps
	go build -o shar-server .

# Run the server
run: build
	./shar-server

# Run tests
test:
	go test -v ./...

# Clean build artifacts
clean:
	rm -f shar-server
	go clean

# Build Docker image
docker-build:
	docker build -t shar:latest .

# Run with Docker Compose
docker-run:
	docker-compose up -d

# Stop Docker Compose services
docker-stop:
	docker-compose down

# Download dependencies
deps:
	go mod download
	go mod tidy

# Format code
fmt:
	go fmt ./...

# Run linter (requires golangci-lint)
lint:
	golangci-lint run

# Development setup
dev-setup: deps
	@echo "Installing development tools..."
	go install github.com/golangci/golangci-lint/cmd/golangci-lint@latest

# Quick development cycle
dev: fmt build run

# Production build
prod-build:
	CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -ldflags '-w -s' -o shar-server .

# Generate Go modules
mod-init:
	go mod init github.com/your-org/shar

# Update dependencies
mod-update:
	go get -u ./...
	go mod tidy