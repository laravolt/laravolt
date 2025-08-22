package main

import (
	"context"
	"log"
	"os"
	"os/signal"
	"syscall"

	"github.com/your-org/shar/config"
	"github.com/your-org/shar/server"
)

func main() {
	// Load configuration
	cfg, err := config.Load()
	if err != nil {
		log.Fatalf("Failed to load configuration: %v", err)
	}

	// Print configuration
	cfg.Print()

	// Create server
	srv := server.New(cfg)

	// Create context for graceful shutdown
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()

	// Handle graceful shutdown
	go func() {
		c := make(chan os.Signal, 1)
		signal.Notify(c, os.Interrupt, syscall.SIGTERM)
		<-c
		log.Println("Shutting down SHAR server...")
		cancel()
	}()

	// Start server
	if err := srv.Start(ctx); err != nil {
		log.Fatalf("Server failed to start: %v", err)
	}
}