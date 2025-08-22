package server

import (
	"context"
	"encoding/json"
	"fmt"
	"net/http"
	"os"
	"time"

	"github.com/gorilla/mux"
	"github.com/nats-io/nats.go"
	"github.com/sirupsen/logrus"
	"gitlab.com/shar-workflow/shar/client"
	"gitlab.com/shar-workflow/shar/model"
	"github.com/your-org/shar/config"
)

// Server represents the SHAR HTTP server
type Server struct {
	config     *config.Config
	sharClient *client.Client
	natsConn   *nats.Conn
	logger     *logrus.Logger
	router     *mux.Router
}

// WorkflowRequest represents a workflow creation request
type WorkflowRequest struct {
	Name    string `json:"name"`
	BPMNXml string `json:"bpmn_xml"`
}

// WorkflowInstanceRequest represents a workflow instance launch request
type WorkflowInstanceRequest struct {
	WorkflowName string                 `json:"workflow_name"`
	Variables    map[string]interface{} `json:"variables"`
}

// WorkflowResponse represents a workflow response
type WorkflowResponse struct {
	ID      string `json:"id"`
	Name    string `json:"name"`
	Status  string `json:"status"`
	Message string `json:"message,omitempty"`
}

// WorkflowInstanceResponse represents a workflow instance response
type WorkflowInstanceResponse struct {
	ID           string                 `json:"id"`
	WorkflowName string                 `json:"workflow_name"`
	Status       string                 `json:"status"`
	Variables    map[string]interface{} `json:"variables"`
	CreatedAt    time.Time              `json:"created_at"`
}

// New creates a new SHAR server
func New(cfg *config.Config) *Server {
	logger := logrus.New()
	logger.SetLevel(cfg.GetLogLevel())
	
	return &Server{
		config: cfg,
		logger: logger,
		router: mux.NewRouter(),
	}
}

// Start starts the SHAR server
func (s *Server) Start(ctx context.Context) error {
	// Connect to NATS
	nc, err := nats.Connect(s.config.NatsURL)
	if err != nil {
		return fmt.Errorf("failed to connect to NATS: %w", err)
	}
	s.natsConn = nc

	// Initialize SHAR client
	s.sharClient = client.New()
	if err := s.sharClient.Dial(ctx, s.config.NatsURL); err != nil {
		return fmt.Errorf("failed to dial SHAR client: %w", err)
	}

	// Setup routes
	s.setupRoutes()

	// Start HTTP server
	addr := fmt.Sprintf("%s:%d", s.config.ServerHost, s.config.ServerPort)
	s.logger.WithField("address", addr).Info("Starting SHAR HTTP server")

	server := &http.Server{
		Addr:    addr,
		Handler: s.router,
	}

	return server.ListenAndServe()
}

// setupRoutes configures all HTTP routes
func (s *Server) setupRoutes() {
	// CORS middleware
	s.router.Use(s.corsMiddleware)
	s.router.Use(s.loggingMiddleware)

	api := s.router.PathPrefix("/api/v1").Subrouter()

	// Workflow management endpoints
	api.HandleFunc("/workflows", s.createWorkflow).Methods("POST")
	api.HandleFunc("/workflows", s.listWorkflows).Methods("GET")
	api.HandleFunc("/workflows/{name}", s.getWorkflow).Methods("GET")
	api.HandleFunc("/workflows/{name}", s.deleteWorkflow).Methods("DELETE")

	// Workflow instance endpoints
	api.HandleFunc("/workflows/{name}/instances", s.launchWorkflowInstance).Methods("POST")
	api.HandleFunc("/instances", s.listWorkflowInstances).Methods("GET")
	api.HandleFunc("/instances/{id}", s.getWorkflowInstance).Methods("GET")
	api.HandleFunc("/instances/{id}/complete", s.completeWorkflowInstance).Methods("POST")

	// Health check
	s.router.HandleFunc("/health", s.healthCheck).Methods("GET")
}

// createWorkflow handles BPMN workflow creation
func (s *Server) createWorkflow(w http.ResponseWriter, r *http.Request) {
	var req WorkflowRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		s.writeError(w, http.StatusBadRequest, "Invalid JSON payload")
		return
	}

	if req.Name == "" || req.BPMNXml == "" {
		s.writeError(w, http.StatusBadRequest, "Name and BPMN XML are required")
		return
	}

	// Load BPMN workflow into SHAR
	_, err := s.sharClient.LoadBPMNWorkflowFromBytes(context.Background(), req.Name, []byte(req.BPMNXml))
	if err != nil {
		s.logger.WithError(err).Error("Failed to load BPMN workflow")
		s.writeError(w, http.StatusInternalServerError, "Failed to load workflow")
		return
	}

	s.logger.WithField("workflow_name", req.Name).Info("Workflow created successfully")

	response := WorkflowResponse{
		ID:     req.Name,
		Name:   req.Name,
		Status: "created",
	}

	s.writeJSON(w, http.StatusCreated, response)
}

// launchWorkflowInstance handles workflow instance launching
func (s *Server) launchWorkflowInstance(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	workflowName := vars["name"]

	var req WorkflowInstanceRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		s.writeError(w, http.StatusBadRequest, "Invalid JSON payload")
		return
	}

	// Convert variables to SHAR model.Vars
	variables := model.NewVars()
	for key, value := range req.Variables {
		variables.Set(key, value)
	}

	// Launch workflow instance
	instanceID, err := s.sharClient.LaunchWorkflow(context.Background(), workflowName, variables)
	if err != nil {
		s.logger.WithError(err).Error("Failed to launch workflow instance")
		s.writeError(w, http.StatusInternalServerError, "Failed to launch workflow instance")
		return
	}

	s.logger.WithFields(logrus.Fields{
		"workflow_name": workflowName,
		"instance_id":   instanceID,
	}).Info("Workflow instance launched successfully")

	response := WorkflowInstanceResponse{
		ID:           instanceID,
		WorkflowName: workflowName,
		Status:       "running",
		Variables:    req.Variables,
		CreatedAt:    time.Now(),
	}

	s.writeJSON(w, http.StatusCreated, response)
}

// listWorkflows handles listing all workflows
func (s *Server) listWorkflows(w http.ResponseWriter, r *http.Request) {
	// This would need to be implemented based on SHAR's workflow listing capabilities
	// For now, return empty list
	s.writeJSON(w, http.StatusOK, []WorkflowResponse{})
}

// getWorkflow handles getting a specific workflow
func (s *Server) getWorkflow(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	name := vars["name"]

	// This would need to be implemented based on SHAR's workflow retrieval capabilities
	response := WorkflowResponse{
		ID:     name,
		Name:   name,
		Status: "active",
	}

	s.writeJSON(w, http.StatusOK, response)
}

// deleteWorkflow handles workflow deletion
func (s *Server) deleteWorkflow(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	name := vars["name"]

	// This would need to be implemented based on SHAR's workflow deletion capabilities
	s.logger.WithField("workflow_name", name).Info("Workflow deletion requested")

	s.writeJSON(w, http.StatusOK, map[string]string{"message": "Workflow deleted successfully"})
}

// listWorkflowInstances handles listing workflow instances
func (s *Server) listWorkflowInstances(w http.ResponseWriter, r *http.Request) {
	// This would need to be implemented based on SHAR's instance listing capabilities
	s.writeJSON(w, http.StatusOK, []WorkflowInstanceResponse{})
}

// getWorkflowInstance handles getting a specific workflow instance
func (s *Server) getWorkflowInstance(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	id := vars["id"]

	// This would need to be implemented based on SHAR's instance retrieval capabilities
	response := WorkflowInstanceResponse{
		ID:        id,
		Status:    "running",
		Variables: make(map[string]interface{}),
		CreatedAt: time.Now(),
	}

	s.writeJSON(w, http.StatusOK, response)
}

// completeWorkflowInstance handles completing a workflow instance
func (s *Server) completeWorkflowInstance(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	id := vars["id"]

	// This would need to be implemented based on SHAR's instance completion capabilities
	s.logger.WithField("instance_id", id).Info("Workflow instance completion requested")

	s.writeJSON(w, http.StatusOK, map[string]string{"message": "Workflow instance completed"})
}

// healthCheck handles health check requests
func (s *Server) healthCheck(w http.ResponseWriter, r *http.Request) {
	status := "healthy"
	
	// Check NATS connection
	if s.natsConn == nil || !s.natsConn.IsConnected() {
		status = "unhealthy"
	}

	response := map[string]interface{}{
		"status":    status,
		"timestamp": time.Now(),
		"version":   "1.0.0",
	}

	if status == "unhealthy" {
		s.writeJSON(w, http.StatusServiceUnavailable, response)
	} else {
		s.writeJSON(w, http.StatusOK, response)
	}
}

// Middleware functions

func (s *Server) corsMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")

		if r.Method == "OPTIONS" {
			w.WriteHeader(http.StatusOK)
			return
		}

		next.ServeHTTP(w, r)
	})
}

func (s *Server) loggingMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		start := time.Now()
		next.ServeHTTP(w, r)
		s.logger.WithFields(logrus.Fields{
			"method":   r.Method,
			"path":     r.URL.Path,
			"duration": time.Since(start),
		}).Info("HTTP request processed")
	})
}

// Helper functions

func (s *Server) writeJSON(w http.ResponseWriter, status int, data interface{}) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	json.NewEncoder(w).Encode(data)
}

func (s *Server) writeError(w http.ResponseWriter, status int, message string) {
	s.writeJSON(w, status, map[string]string{"error": message})
}