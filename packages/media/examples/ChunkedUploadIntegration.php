<?php

/**
 * Laravolt Chunked Upload Integration Example
 * 
 * This file demonstrates how to integrate chunked upload functionality
 * into your Laravel application using Laravolt's ChunkedMediaHandler.
 */

namespace Laravolt\Media\Examples;

use Illuminate\Http\Request;
use Laravolt\Media\MediaHandler\ChunkedMediaHandler;

class ChunkedUploadIntegration
{
    /**
     * Example: Basic chunked upload controller
     */
    public function uploadChunk(Request $request)
    {
        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        return $handler();
    }

    /**
     * Example: Custom validation before chunked upload
     */
    public function uploadChunkWithValidation(Request $request)
    {
        // Custom validation
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
        ]);

        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        return $handler();
    }

    /**
     * Example: Handle chunked upload with custom response
     */
    public function uploadChunkCustomResponse(Request $request)
    {
        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        $response = $handler();
        
        // Customize response
        $data = json_decode($response->getContent(), true);
        
        if ($data['success'] && isset($data['files'])) {
            // Add custom fields to response
            foreach ($data['files'] as &$file) {
                $file['uploaded_at'] = now()->toISOString();
                $file['upload_id'] = uniqid();
            }
        }
        
        return response()->json($data);
    }

    /**
     * Example: Handle chunked upload with user context
     */
    public function uploadChunkWithUserContext(Request $request)
    {
        // Add user context to request
        $request->merge([
            'handler' => 'chunked',
            'user_id' => auth()->id(),
            'upload_context' => 'profile_picture'
        ]);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        return $handler();
    }

    /**
     * Example: Handle chunked upload with progress tracking
     */
    public function uploadChunkWithProgress(Request $request)
    {
        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        $response = $handler();
        
        // Add progress information to response
        $data = json_decode($response->getContent(), true);
        
        if (isset($data['done'])) {
            $data['progress_percentage'] = $data['done'];
            $data['estimated_time_remaining'] = $this->calculateEstimatedTime($data);
        }
        
        return response()->json($data);
    }

    /**
     * Example: Handle chunked upload with custom storage
     */
    public function uploadChunkWithCustomStorage(Request $request)
    {
        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        $response = $handler();
        
        // Custom storage logic after upload
        $data = json_decode($response->getContent(), true);
        
        if ($data['success'] && isset($data['files'])) {
            foreach ($data['files'] as $file) {
                // Store additional metadata
                $this->storeUploadMetadata($file);
                
                // Trigger custom events
                event(new \App\Events\FileUploaded($file));
            }
        }
        
        return $response;
    }

    /**
     * Example: Handle chunked upload with error handling
     */
    public function uploadChunkWithErrorHandling(Request $request)
    {
        try {
            // Set handler to chunked
            $request->merge(['handler' => 'chunked']);
            
            // Create and execute handler
            $handler = new ChunkedMediaHandler($request);
            $response = $handler();
            
            return $response;
            
        } catch (\Exception $e) {
            // Log error
            \Log::error('Chunked upload failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'file_name' => $request->file('file')?->getClientOriginalName(),
                'file_size' => $request->file('file')?->getSize(),
            ]);
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Upload failed. Please try again.',
                'error_code' => 'UPLOAD_FAILED'
            ], 500);
        }
    }

    /**
     * Example: Handle chunked upload with rate limiting
     */
    public function uploadChunkWithRateLimit(Request $request)
    {
        // Check rate limit
        $key = 'chunked_upload:' . auth()->id();
        $maxAttempts = 10; // 10 uploads per minute
        $decayMinutes = 1;
        
        if (\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = \RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Too many upload attempts. Please wait {$seconds} seconds.",
                'retry_after' => $seconds
            ], 429);
        }
        
        // Increment rate limit
        \RateLimiter::hit($key, $decayMinutes * 60);
        
        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        return $handler();
    }

    /**
     * Example: Handle chunked upload with custom middleware
     */
    public function uploadChunkWithMiddleware(Request $request)
    {
        // Apply custom middleware logic
        if (!$this->canUploadFile($request)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to upload files.',
                'error_code' => 'UPLOAD_NOT_ALLOWED'
            ], 403);
        }
        
        // Set handler to chunked
        $request->merge(['handler' => 'chunked']);
        
        // Create and execute handler
        $handler = new ChunkedMediaHandler($request);
        return $handler();
    }

    /**
     * Helper method: Calculate estimated time remaining
     */
    private function calculateEstimatedTime(array $data): ?int
    {
        if (!isset($data['done']) || $data['done'] <= 0) {
            return null;
        }
        
        $progress = $data['done'] / 100;
        $elapsedTime = time() - ($data['start_time'] ?? time());
        $totalEstimatedTime = $elapsedTime / $progress;
        $remainingTime = $totalEstimatedTime - $elapsedTime;
        
        return max(0, (int) $remainingTime);
    }

    /**
     * Helper method: Store upload metadata
     */
    private function storeUploadMetadata(array $file): void
    {
        // Store additional metadata in database
        \DB::table('upload_metadata')->insert([
            'media_id' => $file['data']['id'],
            'user_id' => auth()->id(),
            'original_name' => $file['name'],
            'file_size' => $file['size'],
            'mime_type' => $file['type'],
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Helper method: Check if user can upload file
     */
    private function canUploadFile(Request $request): bool
    {
        // Custom logic to check if user can upload
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Check user permissions
        if (!$user->can('upload_files')) {
            return false;
        }
        
        // Check file type restrictions
        $file = $request->file('file');
        if ($file) {
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return false;
            }
        }
        
        return true;
    }
}