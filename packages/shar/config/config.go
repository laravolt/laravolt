package config

import (
	"fmt"
	"strings"

	"github.com/kelseyhightower/envconfig"
	"github.com/sirupsen/logrus"
)

// Config holds all configuration for SHAR
type Config struct {
	// NATS configuration
	NatsURL string `envconfig:"NATS_URL" default:"nats://127.0.0.1:4222"`
	
	// Logging configuration
	LogLevel string `envconfig:"SHAR_LOG_LEVEL" default:"info"`
	
	// Server configuration
	ServerPort int    `envconfig:"SHAR_PORT" default:"8080"`
	ServerHost string `envconfig:"SHAR_HOST" default:"0.0.0.0"`
	
	// Workflow configuration
	WorkflowTimeout int `envconfig:"SHAR_WORKFLOW_TIMEOUT" default:"300"` // seconds
}

// Load loads configuration from environment variables
func Load() (*Config, error) {
	var cfg Config
	if err := envconfig.Process("", &cfg); err != nil {
		return nil, fmt.Errorf("failed to load configuration: %w", err)
	}
	
	// Validate log level
	if !isValidLogLevel(cfg.LogLevel) {
		return nil, fmt.Errorf("invalid log level: %s. Valid levels are: debug, info, warn, error", cfg.LogLevel)
	}
	
	return &cfg, nil
}

// GetLogLevel returns the logrus log level from string
func (c *Config) GetLogLevel() logrus.Level {
	switch strings.ToLower(c.LogLevel) {
	case "debug":
		return logrus.DebugLevel
	case "warn":
		return logrus.WarnLevel
	case "error":
		return logrus.ErrorLevel
	default:
		return logrus.InfoLevel
	}
}

// isValidLogLevel checks if the provided log level is valid
func isValidLogLevel(level string) bool {
	validLevels := []string{"debug", "info", "warn", "error"}
	level = strings.ToLower(level)
	for _, valid := range validLevels {
		if level == valid {
			return true
		}
	}
	return false
}

// Print prints the current configuration (excluding sensitive data)
func (c *Config) Print() {
	logrus.WithFields(logrus.Fields{
		"nats_url":         c.NatsURL,
		"log_level":        c.LogLevel,
		"server_host":      c.ServerHost,
		"server_port":      c.ServerPort,
		"workflow_timeout": c.WorkflowTimeout,
	}).Info("SHAR configuration loaded")
}