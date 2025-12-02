# Client-Side Upload Guide

This guide will help you set up client-side (direct-to-cloud) upload functionality in your Laravolt application. Client-side uploads are ideal for large files as they bypass your application server and upload directly to cloud storage (S3, R2, MinIO, etc.).

## Overview

### Server-Side vs Client-Side Upload

| Feature | Server-Side (Chunked) | Client-Side (Direct) |
|---------|----------------------|---------------------|
| File Path | Client → Server → Cloud | Client → Cloud (direct) |
| Server Load | High (processes all data) | Low (only generates URLs) |
| Best For | Small to medium files | Large files (>100MB) |
| Complexity | Simple | Moderate |
| Storage Backend | Any (local, cloud) | S3-compatible only |

## Quick Setup (10 minutes)

### 1. Configure S3-Compatible Storage

First, ensure you have an S3-compatible storage configured in `config/filesystems.php`:

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => false,
],
```

For **Cloudflare R2**:

```php
'r2' => [
    'driver' => 's3',
    'key' => env('R2_ACCESS_KEY_ID'),
    'secret' => env('R2_SECRET_ACCESS_KEY'),
    'region' => 'auto',
    'bucket' => env('R2_BUCKET'),
    'endpoint' => env('R2_ENDPOINT'),
    'use_path_style_endpoint' => true,
],
```

### 2. Configure CORS on Your Storage

You must configure CORS on your S3/R2 bucket to allow direct uploads from browsers.

**For Cloudflare R2**, add this CORS policy:

```json
[
  {
    "AllowedOrigins": ["https://your-domain.com"],
    "AllowedMethods": ["GET", "PUT", "POST", "DELETE", "HEAD"],
    "AllowedHeaders": ["*"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3600
  }
]
```

**For AWS S3**, add this CORS configuration:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
    <CORSRule>
        <AllowedOrigin>https://your-domain.com</AllowedOrigin>
        <AllowedMethod>GET</AllowedMethod>
        <AllowedMethod>PUT</AllowedMethod>
        <AllowedMethod>POST</AllowedMethod>
        <AllowedMethod>DELETE</AllowedMethod>
        <AllowedMethod>HEAD</AllowedMethod>
        <AllowedHeader>*</AllowedHeader>
        <ExposeHeader>ETag</ExposeHeader>
        <MaxAgeSeconds>3600</MaxAgeSeconds>
    </CORSRule>
</CORSConfiguration>
```

### 3. Publish Configuration

```bash
php artisan vendor:publish --tag=laravolt-media-config
```

This creates `config/client-upload.php` where you can customize settings.

### 4. Enable Client-Side Upload

Set the following in your `.env` file:

```env
CLIENT_UPLOAD_ENABLED=true
CLIENT_UPLOAD_DISK=s3  # or r2
```

### 5. Publish Assets

```bash
php artisan vendor:publish --tag=laravolt-media-assets
```

This publishes `client-side-uploader.js` to `public/js/components/`.

### 6. Create Your First Client-Side Upload

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client-Side Upload Test</title>
</head>
<body>
    <div id="upload-zone" class="file-drop-zone" style="border: 2px dashed #ccc; padding: 40px; text-align: center;">
        <p>Drop files here or <button class="file-browse-button">Browse</button></p>
        <div id="progress"></div>
        <div id="results"></div>
    </div>

    <script src="{{ asset('js/components/client-side-uploader.js') }}"></script>

    <script>
    const uploader = new ClientSideUploader(document.getElementById('upload-zone'), {
        onProgress: function(upload, progress) {
            document.getElementById('progress').innerHTML =
                `<p>Uploading ${upload.fileName}: ${Math.round(progress * 100)}%</p>`;
        },
        onFileSuccess: function(upload, response) {
            document.getElementById('results').innerHTML +=
                `<p>✅ ${upload.fileName} uploaded successfully!</p>`;
        },
        onFileError: function(upload, error) {
            document.getElementById('results').innerHTML +=
                `<p>❌ ${upload.fileName} failed: ${error}</p>`;
        }
    });
    </script>
</body>
</html>
```

## Configuration Options

### config/client-upload.php

```php
return [
    // Enable/disable client-side upload
    'enabled' => env('CLIENT_UPLOAD_ENABLED', false),

    // Storage disk (must be S3-compatible)
    'disk' => env('CLIENT_UPLOAD_DISK', 's3'),

    // Upload path prefix
    'path_prefix' => env('CLIENT_UPLOAD_PATH_PREFIX', 'uploads'),

    // Presigned URL expiration (minutes)
    'url_expiration' => env('CLIENT_UPLOAD_URL_EXPIRATION', 60),

    // Maximum file size (bytes) - default 5GB
    'max_file_size' => env('CLIENT_UPLOAD_MAX_FILE_SIZE', 5 * 1024 * 1024 * 1024),

    // Multipart upload threshold (bytes) - default 100MB
    'multipart_threshold' => env('CLIENT_UPLOAD_MULTIPART_THRESHOLD', 100 * 1024 * 1024),

    // Multipart chunk size (bytes) - minimum 5MB
    'multipart_chunk_size' => env('CLIENT_UPLOAD_MULTIPART_CHUNK_SIZE', 10 * 1024 * 1024),

    // Maximum concurrent part uploads
    'max_concurrent_uploads' => env('CLIENT_UPLOAD_MAX_CONCURRENT', 4),
];
```

## JavaScript API

### Constructor Options

```javascript
const uploader = new ClientSideUploader(element, {
    // Configuration endpoints
    configEndpoint: '/media/client-upload/config',
    initiateEndpoint: '/media/client-upload/initiate',

    // Upload limits (overridden by server config)
    maxFileSize: 5 * 1024 * 1024 * 1024,
    multipartThreshold: 100 * 1024 * 1024,
    multipartChunkSize: 10 * 1024 * 1024,
    maxConcurrentUploads: 4,

    // Retry configuration
    retryAttempts: 3,
    retryDelay: 1000,

    // File validation
    allowedMimeTypes: null,  // null = allow all
    allowedExtensions: null,
    maxFiles: null,

    // Callbacks
    onFileAdded: function(upload) {},
    onUploadStart: function(upload) {},
    onProgress: function(upload, progress) {},
    onPartComplete: function(upload, part) {},
    onFileSuccess: function(upload, response) {},
    onFileError: function(upload, error) {},
    onComplete: function(uploads) {},
    onAbort: function(upload) {},
});
```

### Methods

```javascript
// Cancel a specific upload
uploader.cancel(uploadId);

// Cancel all uploads
uploader.cancelAll();

// Get a specific upload
const upload = uploader.getUpload(uploadId);

// Get all uploads
const uploads = uploader.getUploads();

// Clear completed/failed/aborted uploads
uploader.clearCompleted();

// Destroy the uploader
uploader.destroy();
```

### Upload Object Properties

```javascript
{
    id: 'upload-123',           // Unique upload ID
    file: File,                 // Original File object
    fileName: 'document.pdf',   // File name
    fileSize: 10485760,         // File size in bytes
    contentType: 'application/pdf',
    status: 'uploading',        // pending, initiating, uploading, complete, error, aborted
    progress: 0.5,              // 0 to 1
    key: 'uploads/2024/01/...',  // Storage key
    uploadToken: '...',         // Server-generated token
    multipartUploadId: '...',   // For multipart uploads
    parts: [...],               // Part information
    completedParts: [...],      // Completed parts with ETags
    error: null,                // Error message if failed
    result: {...},              // Server response on success
}
```

## How It Works

### Simple Upload (< 100MB)

1. Client calls `/media/client-upload/initiate` with file metadata
2. Server validates and returns a presigned PUT URL
3. Client uploads file directly to cloud storage using PUT
4. Client calls `/media/client-upload/complete-simple` to confirm
5. Server validates and saves to media library

### Multipart Upload (>= 100MB)

1. Client calls `/media/client-upload/initiate` with file metadata
2. Server initiates multipart upload and returns uploadId
3. Client requests presigned URLs for each part (`/media/client-upload/presign-part`)
4. Client uploads parts concurrently (up to `maxConcurrentUploads`)
5. Client calls `/media/client-upload/complete-multipart` with all ETags
6. Server completes multipart upload and saves to media library

## Security Considerations

### Upload Token

Every upload is protected by a signed token containing:
- File key
- Original filename
- Expected content type
- Expected file size
- User ID (if authenticated)
- Expiration timestamp

The token is validated on completion to prevent tampering.

### File Validation

- Pre-upload: Validates file size, MIME type, and extension
- Post-upload: Can validate actual file size and MIME type (configurable)

### Rate Limiting

Configure rate limiting in `config/client-upload.php`:

```php
'security' => [
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 30,  // per minute
        'decay_minutes' => 1,
    ],
],
```

## Troubleshooting

### CORS Errors

**Symptom**: Upload fails with "No 'Access-Control-Allow-Origin' header" or `PreflightMissingAllowOriginHeader`

**Solution**:
1. Verify CORS is configured on your bucket
2. Check that `AllowedOrigins` includes your domain
3. Ensure `ExposeHeaders` includes `ETag`
4. **Important**: Make sure `AllowedHeaders` is set to `["*"]` - this is required for preflight requests

**Example of incomplete CORS (will fail)**:
```json
[
  {
    "AllowedOrigins": ["http://localhost:8000", "https://your-domain.com"],
    "AllowedMethods": ["GET", "PUT", "POST"]
  }
]
```

**Complete CORS configuration (correct)**:
```json
[
  {
    "AllowedOrigins": ["http://localhost:8000", "https://your-domain.com"],
    "AllowedMethods": ["GET", "PUT", "POST", "DELETE", "HEAD"],
    "AllowedHeaders": ["*"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3600
  }
]
```

The `PreflightMissingAllowOriginHeader` error typically occurs when `AllowedHeaders` is missing from your CORS policy.

### "Failed to initiate upload"

**Symptom**: Initiate endpoint returns 500 error

**Solution**:
1. Verify S3/R2 credentials are correct
2. Check bucket exists and is accessible
3. Review Laravel logs for detailed error

### Multipart Upload Fails

**Symptom**: Parts upload but completion fails

**Solution**:
1. Ensure all ETags are captured correctly
2. Check that parts are numbered correctly (1-indexed)
3. Verify upload wasn't aborted

### Files Don't Appear in Media Library

**Symptom**: Upload succeeds but no media entry

**Solution**:
1. Check Guest model exists (for anonymous uploads)
2. Verify Spatie Media Library is configured
3. Review logs for media library errors

## Production Checklist

- [ ] Configure CORS on your storage bucket
- [ ] Set appropriate file size limits
- [ ] Configure allowed MIME types and extensions
- [ ] Enable rate limiting
- [ ] Set up proper error handling in callbacks
- [ ] Test with your expected file sizes
- [ ] Monitor upload success/failure rates
- [ ] Set up cleanup for incomplete multipart uploads
- [ ] Configure webhook notifications if needed

## Comparison: When to Use Which

Use **Server-Side (Chunked) Upload** when:
- Files are typically < 100MB
- You need to process files immediately
- You're using local or non-S3 storage
- You need virus scanning on upload

Use **Client-Side (Direct) Upload** when:
- Files are typically > 100MB
- Server bandwidth is limited
- You want to reduce server load
- You're using S3-compatible storage
- You need resumable uploads for very large files
