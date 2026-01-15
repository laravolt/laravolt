<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Chunked Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for chunked file uploads in Laravolt Media package.
    | These settings control how chunked uploads are handled and processed.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Chunk Size
    |--------------------------------------------------------------------------
    |
    | The default chunk size in bytes. This can be overridden by client-side
    | configuration. Recommended values:
    | - 1MB (1048576) for files < 10MB
    | - 2MB (2097152) for files 10-100MB
    | - 5MB (5242880) for files > 100MB
    |
    */
    'default_chunk_size' => 2 * 1024 * 1024, // 2MB

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum allowed file size for chunked uploads in bytes.
    | Set to null for no limit (not recommended).
    |
    */
    'max_file_size' => 100 * 1024 * 1024, // 100MB

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | Array of allowed MIME types for chunked uploads.
    | Set to null to allow all file types (not recommended).
    |
    */
    'allowed_mime_types' => [
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        // Text files
        'text/plain',
        'text/csv',
        'application/json',
        'application/xml',
        // Archives
        'application/zip',
        'application/x-rar-compressed',
        'application/x-tar',
        'application/gzip',
        // Audio
        'audio/mpeg',
        'audio/wav',
        'audio/ogg',
        // Video
        'video/mp4',
        'video/avi',
        'video/quicktime',
        'video/x-msvideo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for chunk storage and final file storage.
    |
    */
    'storage' => [
        /*
        |--------------------------------------------------------------------------
        | Chunks Storage Disk
        |--------------------------------------------------------------------------
        |
        | The disk where chunks are temporarily stored during upload.
        | Recommended to use 'local' for performance.
        |
        */
        'chunks_disk' => 'local',

        /*
        |--------------------------------------------------------------------------
        | Chunks Storage Path
        |--------------------------------------------------------------------------
        |
        | The path within the chunks disk where chunks are stored.
        |
        */
        'chunks_path' => 'chunks',

        /*
        |--------------------------------------------------------------------------
        | Final Storage Disk
        |--------------------------------------------------------------------------
        |
        | The disk where final assembled files are stored.
        | This should match your media library configuration.
        |
        */
        'final_disk' => env('MEDIA_DISK', 'public'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic cleanup of stale chunks.
    |
    */
    'cleanup' => [
        /*
        |--------------------------------------------------------------------------
        | Stale After Hours
        |--------------------------------------------------------------------------
        |
        | Number of hours after which chunks are considered stale and can be
        | cleaned up. Recommended: 24 hours for daily cleanup.
        |
        */
        'stale_after_hours' => 24,

        /*
        |--------------------------------------------------------------------------
        | Auto Cleanup
        |--------------------------------------------------------------------------
        |
        | Whether to automatically register the cleanup command in the scheduler.
        | If true, cleanup will run daily at the specified time.
        |
        */
        'auto_cleanup' => env('CHUNKED_UPLOAD_AUTO_CLEANUP', true),

        /*
        |--------------------------------------------------------------------------
        | Cleanup Time
        |--------------------------------------------------------------------------
        |
        | Time when automatic cleanup should run (24-hour format).
        |
        */
        'cleanup_time' => '02:00',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    |
    | Validation rules and settings for chunked uploads.
    |
    */
    'validation' => [
        /*
        |--------------------------------------------------------------------------
        | Validate Checksums
        |--------------------------------------------------------------------------
        |
        | Whether to validate chunk checksums for data integrity.
        | Provides additional security but may impact performance.
        |
        */
        'validate_checksums' => env('CHUNKED_UPLOAD_VALIDATE_CHECKSUMS', false),

        /*
        |--------------------------------------------------------------------------
        | Max Chunks Per File
        |--------------------------------------------------------------------------
        |
        | Maximum number of chunks allowed per file.
        | Prevents abuse and limits server resource usage.
        |
        */
        'max_chunks_per_file' => 1000,

        /*
        |--------------------------------------------------------------------------
        | Filename Validation
        |--------------------------------------------------------------------------
        |
        | Regular expression for validating uploaded filenames.
        | Helps prevent directory traversal and other security issues.
        |
        */
        'filename_pattern' => '/^[a-zA-Z0-9._\-\s()]+$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Settings that affect upload performance and server resources.
    |
    */
    'performance' => [
        /*
        |--------------------------------------------------------------------------
        | Max Simultaneous Uploads
        |--------------------------------------------------------------------------
        |
        | Maximum number of simultaneous chunk uploads per session.
        | Helps prevent server overload.
        |
        */
        'max_simultaneous_uploads' => 3,

        /*
        |--------------------------------------------------------------------------
        | Chunk Assembly Timeout
        |--------------------------------------------------------------------------
        |
        | Maximum time in seconds to wait for chunk assembly.
        |
        */
        'assembly_timeout' => 300, // 5 minutes

        /*
        |--------------------------------------------------------------------------
        | Memory Limit for Assembly
        |--------------------------------------------------------------------------
        |
        | Memory limit for chunk assembly process.
        | Set to null to use system default.
        |
        */
        'assembly_memory_limit' => '256M',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings for chunked uploads.
    |
    */
    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Rate Limiting
        |--------------------------------------------------------------------------
        |
        | Rate limiting configuration for chunk upload endpoints.
        |
        */
        'rate_limit' => [
            'enabled' => env('CHUNKED_UPLOAD_RATE_LIMIT', true),
            'max_attempts' => 60, // requests per minute
            'decay_minutes' => 1,
        ],

        /*
        |--------------------------------------------------------------------------
        | IP Whitelist
        |--------------------------------------------------------------------------
        |
        | Array of IP addresses allowed to use chunked upload.
        | Set to null to allow all IPs.
        |
        */
        'ip_whitelist' => null,

        /*
        |--------------------------------------------------------------------------
        | Virus Scanning
        |--------------------------------------------------------------------------
        |
        | Configuration for virus scanning of uploaded files.
        |
        */
        'virus_scan' => [
            'enabled' => env('CHUNKED_UPLOAD_VIRUS_SCAN', false),
            'command' => 'clamscan --no-summary --infected %s',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging settings for chunked upload operations.
    |
    */
    'logging' => [
        /*
        |--------------------------------------------------------------------------
        | Log Channel
        |--------------------------------------------------------------------------
        |
        | The log channel to use for chunked upload logs.
        |
        */
        'channel' => env('CHUNKED_UPLOAD_LOG_CHANNEL', 'daily'),

        /*
        |--------------------------------------------------------------------------
        | Log Level
        |--------------------------------------------------------------------------
        |
        | Minimum log level for chunked upload operations.
        | Options: emergency, alert, critical, error, warning, notice, info, debug
        |
        */
        'level' => env('CHUNKED_UPLOAD_LOG_LEVEL', 'info'),

        /*
        |--------------------------------------------------------------------------
        | Log Successful Uploads
        |--------------------------------------------------------------------------
        |
        | Whether to log successful upload completions.
        |
        */
        'log_successful_uploads' => env('CHUNKED_UPLOAD_LOG_SUCCESS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for frontend JavaScript components.
    |
    */
    'frontend' => [
        /*
        |--------------------------------------------------------------------------
        | Default Client
        |--------------------------------------------------------------------------
        |
        | Default client library to use: 'resumable' or 'filepond'
        |
        */
        'default_client' => 'resumable',

        /*
        |--------------------------------------------------------------------------
        | CDN URLs
        |--------------------------------------------------------------------------
        |
        | CDN URLs for client libraries. Set to null to use local files.
        |
        */
        'cdn' => [
            'resumable' => 'https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js',
            'filepond' => [
                'js' => 'https://unpkg.com/filepond/dist/filepond.min.js',
                'css' => 'https://unpkg.com/filepond/dist/filepond.css',
                'plugins' => [
                    'file-validate-size' => 'https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js',
                    'file-validate-type' => 'https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js',
                ],
            ],
        ],
    ],
];
