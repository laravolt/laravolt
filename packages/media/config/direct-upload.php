<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Direct Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for direct file uploads to cloud storage.
    | This feature enables uploading files directly to S3 or other cloud
    | storage providers, bypassing temporary local storage.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk
    |--------------------------------------------------------------------------
    |
    | The default storage disk to use for direct uploads.
    | This should be configured in config/filesystems.php
    | Recommended: 's3' for AWS S3 uploads
    |
    */
    'disk' => env('DIRECT_UPLOAD_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Default Media Collection
    |--------------------------------------------------------------------------
    |
    | The default media collection name for uploaded files.
    |
    */
    'collection' => env('DIRECT_UPLOAD_COLLECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum allowed file size for direct uploads in kilobytes.
    | Default: 100MB (102400 KB)
    |
    */
    'max_file_size' => env('DIRECT_UPLOAD_MAX_SIZE', 102400), // 100MB in KB

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | Array of allowed MIME types for direct uploads.
    | Set to null to allow all file types.
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
    | Temporary URL Expiration
    |--------------------------------------------------------------------------
    |
    | Expiration time for temporary signed URLs (in minutes).
    | Only applies when using presigned URLs for direct upload.
    | Default: 60 minutes
    |
    */
    'temporary_url_expiration' => env('DIRECT_UPLOAD_URL_EXPIRATION', 60),

    /*
    |--------------------------------------------------------------------------
    | S3 Configuration
    |--------------------------------------------------------------------------
    |
    | Specific configuration for AWS S3 direct uploads.
    |
    */
    's3' => [
        /*
        |--------------------------------------------------------------------------
        | Enable Presigned URLs
        |--------------------------------------------------------------------------
        |
        | When enabled, the component will generate presigned URLs for
        | direct browser-to-S3 uploads without going through your server.
        |
        */
        'use_presigned_urls' => env('DIRECT_UPLOAD_S3_PRESIGNED', false),

        /*
        |--------------------------------------------------------------------------
        | Public URLs
        |--------------------------------------------------------------------------
        |
        | Whether uploaded files should be publicly accessible.
        |
        */
        'public' => env('DIRECT_UPLOAD_S3_PUBLIC', true),

        /*
        |--------------------------------------------------------------------------
        | ACL (Access Control List)
        |--------------------------------------------------------------------------
        |
        | The default ACL for uploaded files.
        | Options: private, public-read, public-read-write, authenticated-read
        |
        */
        'acl' => env('DIRECT_UPLOAD_S3_ACL', 'public-read'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Additional validation rules for direct uploads.
    |
    */
    'validation' => [
        /*
        |--------------------------------------------------------------------------
        | Validate Mime Type
        |--------------------------------------------------------------------------
        |
        | Whether to validate file MIME type against allowed types.
        |
        */
        'validate_mime' => env('DIRECT_UPLOAD_VALIDATE_MIME', true),

        /*
        |--------------------------------------------------------------------------
        | Custom Validation Rules
        |--------------------------------------------------------------------------
        |
        | Additional Laravel validation rules to apply to uploaded files.
        | Example: ['dimensions:min_width=100,min_height=200']
        |
        */
        'custom_rules' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security-related settings for direct uploads.
    |
    */
    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Require Authentication
        |--------------------------------------------------------------------------
        |
        | Whether to require user authentication for uploads.
        | If false, Guest model will be used for anonymous uploads.
        |
        */
        'require_auth' => env('DIRECT_UPLOAD_REQUIRE_AUTH', false),

        /*
        |--------------------------------------------------------------------------
        | Rate Limiting
        |--------------------------------------------------------------------------
        |
        | Rate limiting configuration for upload endpoints.
        |
        */
        'rate_limit' => [
            'enabled' => env('DIRECT_UPLOAD_RATE_LIMIT', true),
            'max_attempts' => 60, // uploads per hour
            'decay_minutes' => 60,
        ],
    ],
];
