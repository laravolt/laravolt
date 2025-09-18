<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chunked Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for chunked file upload functionality
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Chunk Size
    |--------------------------------------------------------------------------
    |
    | Default chunk size in bytes. Recommended: 1-2MB for most servers
    |
    */
    'chunk_size' => env('CHUNKED_UPLOAD_CHUNK_SIZE', 2 * 1024 * 1024), // 2MB

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum total file size allowed for chunked uploads
    |
    */
    'max_file_size' => env('CHUNKED_UPLOAD_MAX_FILE_SIZE', 100 * 1024 * 1024), // 100MB

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | MIME types allowed for chunked uploads. Leave empty to allow all types.
    |
    */
    'allowed_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'video/mp4',
        'video/avi',
        'video/mov',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Chunk Storage
    |--------------------------------------------------------------------------
    |
    | Configuration for storing chunks temporarily
    |
    */
    'storage' => [
        'disk' => env('CHUNKED_UPLOAD_DISK', 'local'),
        'path' => env('CHUNKED_UPLOAD_PATH', 'chunks'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic cleanup of old chunks
    |
    */
    'cleanup' => [
        'enabled' => env('CHUNKED_UPLOAD_CLEANUP_ENABLED', true),
        'max_age_hours' => env('CHUNKED_UPLOAD_CLEANUP_MAX_AGE', 24),
        'schedule' => env('CHUNKED_UPLOAD_CLEANUP_SCHEDULE', 'daily'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for chunk upload endpoints
    |
    */
    'rate_limit' => [
        'enabled' => env('CHUNKED_UPLOAD_RATE_LIMIT_ENABLED', true),
        'max_attempts' => env('CHUNKED_UPLOAD_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('CHUNKED_UPLOAD_RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for frontend implementations
    |
    */
    'frontend' => [
        'resumable' => [
            'chunk_size' => env('CHUNKED_UPLOAD_FRONTEND_CHUNK_SIZE', 2 * 1024 * 1024),
            'simultaneous_uploads' => env('CHUNKED_UPLOAD_SIMULTANEOUS_UPLOADS', 3),
            'test_chunks' => env('CHUNKED_UPLOAD_TEST_CHUNKS', true),
        ],
        'filepond' => [
            'chunk_size' => env('CHUNKED_UPLOAD_FRONTEND_CHUNK_SIZE', 2 * 1024 * 1024),
            'chunk_retry_delays' => [0, 1000, 3000, 5000],
            'max_file_size' => '100MB',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related configuration
    |
    */
    'security' => [
        'validate_file_type' => env('CHUNKED_UPLOAD_VALIDATE_FILE_TYPE', true),
        'validate_file_size' => env('CHUNKED_UPLOAD_VALIDATE_FILE_SIZE', true),
        'scan_for_viruses' => env('CHUNKED_UPLOAD_SCAN_VIRUSES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Configuration
    |--------------------------------------------------------------------------
    |
    | Debug and logging configuration
    |
    */
    'debug' => [
        'enabled' => env('CHUNKED_UPLOAD_DEBUG', false),
        'log_chunks' => env('CHUNKED_UPLOAD_LOG_CHUNKS', false),
        'log_errors' => env('CHUNKED_UPLOAD_LOG_ERRORS', true),
    ],
];