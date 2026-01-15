<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Client-Side Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for client-side (direct-to-cloud) file uploads using
    | presigned URLs. This allows files to be uploaded directly from the
    | browser to cloud storage (R2, S3, etc.) without going through the
    | Laravel server, improving efficiency for large file uploads.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Enable Client-Side Upload
    |--------------------------------------------------------------------------
    |
    | Enable or disable client-side upload functionality. When disabled,
    | uploads will fall back to the traditional server-side upload.
    |
    */
    'enabled' => env('CLIENT_UPLOAD_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The storage disk to use for client-side uploads. This should be
    | configured as an S3-compatible disk (S3, R2, MinIO, etc.).
    |
    */
    'disk' => env('CLIENT_UPLOAD_DISK', env('MEDIA_DISK', 's3')),

    /*
    |--------------------------------------------------------------------------
    | Upload Path Prefix
    |--------------------------------------------------------------------------
    |
    | The path prefix for uploaded files. Files will be stored in this
    | directory within the bucket.
    |
    */
    'path_prefix' => env('CLIENT_UPLOAD_PATH_PREFIX', 'uploads'),

    /*
    |--------------------------------------------------------------------------
    | Presigned URL Expiration
    |--------------------------------------------------------------------------
    |
    | How long presigned URLs remain valid, in minutes.
    |
    */
    'url_expiration' => env('CLIENT_UPLOAD_URL_EXPIRATION', 60),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum allowed file size in bytes. Default is 5GB (S3/R2 limit).
    |
    */
    'max_file_size' => env('CLIENT_UPLOAD_MAX_FILE_SIZE', 5 * 1024 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | Multipart Upload Threshold
    |--------------------------------------------------------------------------
    |
    | File size threshold in bytes above which multipart upload will be used.
    | Default is 100MB. Files smaller than this will use single PUT upload.
    |
    */
    'multipart_threshold' => env('CLIENT_UPLOAD_MULTIPART_THRESHOLD', 100 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | Multipart Chunk Size
    |--------------------------------------------------------------------------
    |
    | Size of each part in multipart uploads, in bytes.
    | Minimum is 5MB (S3/R2 requirement), default is 10MB.
    |
    */
    'multipart_chunk_size' => env('CLIENT_UPLOAD_MULTIPART_CHUNK_SIZE', 10 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | Maximum Concurrent Uploads
    |--------------------------------------------------------------------------
    |
    | Maximum number of parts that can be uploaded concurrently.
    |
    */
    'max_concurrent_uploads' => env('CLIENT_UPLOAD_MAX_CONCURRENT', 4),

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | Array of allowed MIME types for client-side uploads.
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
        'video/webm',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Extensions
    |--------------------------------------------------------------------------
    |
    | Array of allowed file extensions for client-side uploads.
    | This provides an additional layer of validation.
    |
    */
    'allowed_extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'csv', 'json', 'xml',
        'zip', 'rar', 'tar', 'gz',
        'mp3', 'wav', 'ogg',
        'mp4', 'avi', 'mov', 'webm',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings for client-side uploads.
    |
    */
    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Rate Limiting
        |--------------------------------------------------------------------------
        |
        | Rate limiting for presigned URL generation endpoints.
        |
        */
        'rate_limit' => [
            'enabled' => env('CLIENT_UPLOAD_RATE_LIMIT', true),
            'max_attempts' => 30, // requests per minute
            'decay_minutes' => 1,
        ],

        /*
        |--------------------------------------------------------------------------
        | Require Authentication
        |--------------------------------------------------------------------------
        |
        | Whether to require authentication for client-side uploads.
        | If false, anonymous uploads are allowed (uses Guest model).
        |
        */
        'require_auth' => env('CLIENT_UPLOAD_REQUIRE_AUTH', false),

        /*
        |--------------------------------------------------------------------------
        | Validate File After Upload
        |--------------------------------------------------------------------------
        |
        | Whether to validate the file after it's uploaded to cloud storage.
        | This adds an extra security check but may slow down the confirmation.
        |
        */
        'validate_after_upload' => env('CLIENT_UPLOAD_VALIDATE_AFTER', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for upload completion callbacks.
    |
    */
    'callbacks' => [
        /*
        |--------------------------------------------------------------------------
        | Enable Webhook
        |--------------------------------------------------------------------------
        |
        | Enable webhook notifications after successful uploads.
        |
        */
        'webhook_enabled' => env('CLIENT_UPLOAD_WEBHOOK_ENABLED', false),

        /*
        |--------------------------------------------------------------------------
        | Webhook URL
        |--------------------------------------------------------------------------
        |
        | URL to send webhook notifications to after successful uploads.
        |
        */
        'webhook_url' => env('CLIENT_UPLOAD_WEBHOOK_URL'),

        /*
        |--------------------------------------------------------------------------
        | Webhook Secret
        |--------------------------------------------------------------------------
        |
        | Secret key used to sign webhook payloads.
        |
        */
        'webhook_secret' => env('CLIENT_UPLOAD_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging settings for client-side upload operations.
    |
    */
    'logging' => [
        'channel' => env('CLIENT_UPLOAD_LOG_CHANNEL', 'daily'),
        'level' => env('CLIENT_UPLOAD_LOG_LEVEL', 'info'),
        'log_successful_uploads' => env('CLIENT_UPLOAD_LOG_SUCCESS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    |
    | CORS headers for client-side uploads. These are applied to presigned URLs
    | and should match your cloud storage CORS configuration.
    |
    */
    'cors' => [
        'allowed_origins' => env('CLIENT_UPLOAD_CORS_ORIGINS', '*'),
        'allowed_methods' => ['GET', 'PUT', 'POST', 'DELETE', 'HEAD'],
        'allowed_headers' => ['Content-Type', 'Content-MD5', 'x-amz-*'],
        'exposed_headers' => ['ETag'],
        'max_age' => 3600,
    ],
];
