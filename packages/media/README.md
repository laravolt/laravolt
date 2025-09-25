# Laravolt Media Package

The Laravolt Media package provides file upload and media management capabilities with support for both traditional and chunked uploads.

## Features

- **Traditional Upload**: Direct file upload using `RedactorMediaHandler` and `FileuploaderMediaHandler`
- **Chunked Upload**: Large file upload support using `ChunkedMediaHandler` with client-side chunking
- **Multiple Clients**: Support for Resumable.js and FilePond
- **Guest Support**: Anonymous file uploads using Guest model
- **Media Library Integration**: Seamless integration with Spatie Media Library
- **Cleanup Jobs**: Automatic cleanup of stale chunk files
- **Validation**: File size, type, and security validation

## Installation

The media package is included with Laravolt by default. For chunked upload functionality, ensure you have the required dependency:

```bash
composer require pion/laravel-chunk-upload
```

## Basic Usage

### Traditional Upload

```php
// Using fileuploader handler
$response = $this->post('/media/media', [
    'handler' => 'fileuploader',
    '_action' => 'upload',
    '_key' => 'file', // form field name
    'file' => $uploadedFile
]);

// Using redactor handler  
$response = $this->post('/media/media', [
    'handler' => 'redactor',
    'file' => [$uploadedFile1, $uploadedFile2]
]);
```

### Chunked Upload

```php
// Upload chunks
$response = $this->post('/media/chunk', [
    'file' => $chunkFile,
    'resumableChunkNumber' => 1,
    'resumableChunkSize' => 2097152, // 2MB
    'resumableTotalSize' => 10485760, // 10MB total
    'resumableIdentifier' => 'unique-file-id',
    'resumableFilename' => 'large-file.pdf',
]);
```

## Frontend Implementation

### Resumable.js Integration

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chunked Upload Example</title>
</head>
<body>
    <div id="drop-zone" class="drop-zone">
        <p>Drop files here or <button id="browse-button">Browse</button></p>
        <div id="file-list"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
    <script src="{{ asset('js/components/chunked-uploader.js') }}"></script>
    
    <script>
    const uploader = new ChunkedUploader(document.getElementById('drop-zone'), {
        chunkSize: 2 * 1024 * 1024, // 2MB chunks
        maxFileSize: 100 * 1024 * 1024, // 100MB max
        onFileSuccess: function(file, response) {
            console.log('Upload completed:', response.files[0]);
        },
        onFileError: function(file, error) {
            console.error('Upload failed:', error);
        },
        onFileProgress: function(file, progress) {
            console.log('Progress:', Math.round(progress * 100) + '%');
        }
    });
    </script>
</body>
</html>
```

### FilePond Integration

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <title>FilePond Chunked Upload</title>
</head>
<body>
    <input type="file" 
           class="filepond" 
           name="files[]" 
           multiple
           data-chunked-uploader='{"maxFiles": 5, "chunkSize": 1048576}'>

    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="{{ asset('js/components/chunked-uploader.js') }}"></script>
</body>
</html>
```

### Auto-Initialize with Data Attributes

```html
<div data-chunked-uploader 
     data-chunked-uploader-options='{"chunkSize": 1048576, "maxFiles": 3}'
     class="drop-zone">
    <p>Drop files here or <span class="file-browse-button">Browse</span></p>
</div>
```

## Configuration

### Chunk Size Recommendations

- **Small files (< 10MB)**: 1MB chunks
- **Medium files (10-100MB)**: 2MB chunks  
- **Large files (> 100MB)**: 5MB chunks
- **Very large files (> 1GB)**: 10MB chunks

### Server Configuration

The chunked upload doesn't require changes to `php.ini` or nginx configuration, but you may want to optimize:

```ini
# php.ini (optional optimizations)
max_execution_time = 300
memory_limit = 256M
post_max_size = 10M        # Per chunk, not total file
upload_max_filesize = 10M  # Per chunk, not total file
```

### Laravel Configuration

```php
// config/filesystems.php
'disks' => [
    'chunks' => [
        'driver' => 'local',
        'root' => storage_path('app/chunks'),
    ],
],
```

## API Endpoints

### Chunked Upload Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/media/chunk` | Upload file chunks |
| GET | `/media/chunk/status` | Check upload status |
| POST | `/media/chunk/complete` | Complete upload (if needed) |
| DELETE | `/media/{id}` | Delete uploaded media |

### Response Format

All handlers return consistent JSON responses:

```json
{
    "success": true,
    "files": [
        {
            "file": "https://example.com/storage/media/file.pdf",
            "name": "document.pdf", 
            "size": 1048576,
            "type": "application/pdf",
            "data": {
                "id": 123,
                "url": "https://example.com/storage/media/file.pdf",
                "thumbnail": "https://example.com/storage/media/thumb.jpg"
            }
        }
    ]
}
```

## Validation

### File Size Validation

```php
// In your form request or controller
$request->validate([
    'file' => 'required|file|max:102400', // 100MB in KB
    'resumableTotalSize' => 'required|integer|max:104857600', // 100MB in bytes
]);
```

### File Type Validation

```php
$request->validate([
    'file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:51200', // 50MB
]);
```

### Frontend Validation

```javascript
const uploader = new ChunkedUploader(element, {
    fileType: ['image/*', 'application/pdf'],
    maxFileSize: 50 * 1024 * 1024, // 50MB
    maxFiles: 5,
    onFileAdded: function(file) {
        if (file.size > 50 * 1024 * 1024) {
            alert('File too large! Maximum size is 50MB.');
            return false;
        }
    }
});
```

## Cleanup and Maintenance

### Automatic Cleanup

Schedule the cleanup command in your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean up chunks older than 24 hours, daily at 2 AM
    $schedule->command('media:cleanup-chunks --hours=24')
             ->dailyAt('02:00');
}
```

### Manual Cleanup

```bash
# Clean up chunks older than 24 hours
php artisan media:cleanup-chunks

# Clean up chunks older than 48 hours
php artisan media:cleanup-chunks --hours=48

# Run cleanup in background queue
php artisan media:cleanup-chunks --queue
```

## Error Handling

### Common Issues and Solutions

#### 413 Request Entity Too Large
This error should not occur with chunked uploads since each chunk is small. If you still get this error:

1. Check your chunk size configuration
2. Verify nginx/Apache configuration
3. Ensure chunks are being sent individually

#### Chunks Not Assembling
If chunks upload but don't create a final file:

1. Check storage permissions
2. Verify chunk storage path exists
3. Check server logs for assembly errors

#### Resume Not Working
If upload resume fails:

1. Verify testChunks is enabled
2. Check that chunk identifiers are consistent
3. Ensure storage persists between requests

### Error Response Format

```json
{
    "success": false,
    "message": "Upload missing file exception"
}
```

## Advanced Usage

### Custom Progress Tracking

```javascript
const uploader = new ChunkedUploader(element, {
    onFileProgress: function(file, progress) {
        const percentage = Math.round(progress * 100);
        document.getElementById('progress-bar').style.width = percentage + '%';
        document.getElementById('progress-text').textContent = percentage + '%';
    }
});
```

### Custom Success Handler

```javascript
const uploader = new ChunkedUploader(element, {
    onFileSuccess: function(file, response) {
        const media = response.files[0];
        
        // Add to gallery
        const gallery = document.getElementById('uploaded-files');
        const item = document.createElement('div');
        item.innerHTML = `
            <img src="${media.data.thumbnail}" alt="${media.name}">
            <p>${media.name}</p>
            <button onclick="deleteMedia(${media.data.id})">Delete</button>
        `;
        gallery.appendChild(item);
    }
});

function deleteMedia(id) {
    fetch(`/media/${id}`, { method: 'DELETE' })
        .then(() => location.reload());
}
```

### Multiple Upload Zones

```javascript
// Different configurations for different zones
const documentUploader = new ChunkedUploader(document.getElementById('documents'), {
    fileType: ['application/pdf', 'application/msword'],
    maxFileSize: 50 * 1024 * 1024,
    chunkSize: 2 * 1024 * 1024
});

const imageUploader = new ChunkedUploader(document.getElementById('images'), {
    fileType: ['image/*'],
    maxFileSize: 10 * 1024 * 1024,
    chunkSize: 1 * 1024 * 1024
});
```

## Security Considerations

1. **File Type Validation**: Always validate file types on both client and server
2. **Size Limits**: Set appropriate file size limits
3. **Rate Limiting**: Consider rate limiting upload endpoints
4. **Authentication**: Use appropriate authentication for sensitive uploads
5. **Virus Scanning**: Consider integrating virus scanning for uploaded files
6. **Storage Security**: Ensure uploaded files are stored securely

## Performance Tips

1. **Chunk Size**: Balance between network efficiency and memory usage
2. **Concurrent Uploads**: Limit simultaneous uploads to prevent server overload
3. **Storage**: Use appropriate storage backend (local, S3, etc.)
4. **CDN**: Consider CDN for serving uploaded media
5. **Cleanup**: Regular cleanup prevents disk space issues

## Browser Compatibility

### Resumable.js
- Chrome 6+
- Firefox 4+
- Safari 6+
- Internet Explorer 10+
- Edge (all versions)

### FilePond
- Chrome 28+
- Firefox 23+
- Safari 7+
- Internet Explorer 11+
- Edge (all versions)

## Testing

Run the test suite:

```bash
# Run all media tests
php artisan test tests/Feature/Media/
php artisan test tests/Unit/Media/

# Run specific test
php artisan test tests/Feature/Media/ChunkedUploadTest.php
```

## Troubleshooting

### Debug Mode

Enable debug logging in your handler:

```php
// In ChunkedMediaHandler.php
protected function upload(): JsonResponse
{
    try {
        \Log::info('Chunk upload request', request()->all());
        
        // ... existing code
    } catch (\Exception $e) {
        \Log::error('Chunk upload error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => request()->all()
        ]);
        throw $e;
    }
}
```

### Common Log Messages

```bash
# Successful chunk upload
[INFO] Chunk upload request: {"resumableChunkNumber":1,...}

# Chunk assembly complete  
[INFO] File assembled successfully: /tmp/chunks/file.pdf

# Cleanup completed
[INFO] Chunked upload cleanup completed: {"deleted_directories":5,...}

# Error cases
[ERROR] Chunk upload error: {"error":"Upload missing file exception",...}
```

## Contributing

When contributing to the media package:

1. Follow PSR-12 coding standards
2. Add tests for new functionality
3. Update documentation
4. Ensure backward compatibility
5. Test with both Resumable.js and FilePond

## License

The Laravolt Media package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).