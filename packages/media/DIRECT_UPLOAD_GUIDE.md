# Direct Upload to Cloud Storage Guide

This guide explains how to use the Direct Upload feature for uploading files directly to cloud storage (S3, etc.) without going through temporary local storage, making it compatible with cloud architectures.

## Overview

The Direct Upload component uses Livewire 3.x to provide a seamless file upload experience that uploads files directly to your configured cloud storage disk (e.g., S3). This approach is ideal for:

- Cloud-based applications where local temporary storage is limited or unavailable
- Applications that need to scale horizontally without shared storage
- Reducing server load by bypassing local file handling
- Improving upload reliability in distributed systems

## Quick Setup

### 1. Configure Your S3 Disk

Ensure your `config/filesystems.php` has S3 configured:

```php
'disks' => [
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
],
```

### 2. Set Environment Variables

Add to your `.env` file:

```env
# AWS S3 Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name

# Direct Upload Configuration
DIRECT_UPLOAD_DISK=s3
DIRECT_UPLOAD_COLLECTION=default
DIRECT_UPLOAD_MAX_SIZE=102400  # 100MB in KB
```

### 3. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=laravolt-media-config
```

This creates `config/direct-upload.php` where you can customize settings.

### 4. Use the Component in Your Blade Views

#### Basic Usage

```blade
<livewire:media::direct-upload />
```

#### Custom Configuration

```blade
<livewire:media::direct-upload 
    disk="s3" 
    collection="documents" 
    :max-file-size="51200" 
/>
```

## Configuration Options

### Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `disk` | string | `s3` | Storage disk to use (must be configured in filesystems.php) |
| `collection` | string | `default` | Media collection name |
| `maxFileSize` | int | `102400` | Maximum file size in KB (default: 100MB) |

### Config File Options

The `config/direct-upload.php` file provides these options:

```php
return [
    // Default storage disk
    'disk' => env('DIRECT_UPLOAD_DISK', 's3'),
    
    // Default collection name
    'collection' => env('DIRECT_UPLOAD_COLLECTION', 'default'),
    
    // Max file size in KB
    'max_file_size' => env('DIRECT_UPLOAD_MAX_SIZE', 102400),
    
    // Allowed MIME types
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'application/pdf',
        // ... more types
    ],
    
    // S3-specific settings
    's3' => [
        'use_presigned_urls' => false,
        'public' => true,
        'acl' => 'public-read',
    ],
];
```

## Usage Examples

### Example 1: Document Upload

```blade
<div class="document-upload">
    <h3>Upload Documents</h3>
    <livewire:media::direct-upload 
        disk="s3"
        collection="documents"
        :max-file-size="51200"
    />
</div>
```

### Example 2: Image Gallery Upload

```blade
<div class="gallery-upload">
    <h3>Add Images to Gallery</h3>
    <livewire:media::direct-upload 
        disk="s3"
        collection="gallery"
        :max-file-size="10240"
    />
</div>
```

### Example 3: With Custom Event Handling

```blade
<div>
    <livewire:media::direct-upload 
        disk="s3"
        collection="attachments"
    />
</div>

<script>
    // Listen for upload success
    window.addEventListener('fileUploaded', event => {
        console.log('File uploaded with ID:', event.detail);
        // Refresh your file list, show notification, etc.
    });
    
    // Listen for file removal
    window.addEventListener('fileRemoved', event => {
        console.log('File removed with ID:', event.detail);
    });
</script>
```

### Example 4: Multiple Upload Zones

```blade
<div class="row">
    <div class="col-md-6">
        <h4>PDF Documents</h4>
        <livewire:media::direct-upload 
            disk="s3"
            collection="pdfs"
            :max-file-size="51200"
        />
    </div>
    
    <div class="col-md-6">
        <h4>Images</h4>
        <livewire:media::direct-upload 
            disk="s3"
            collection="images"
            :max-file-size="10240"
        />
    </div>
</div>
```

## How It Works

1. **User selects a file** - The file is selected via the file input or drag-and-drop
2. **Livewire processes upload** - Livewire temporarily stores the file using its built-in file upload handling
3. **Direct to cloud** - The component immediately moves the file to your configured cloud disk (S3)
4. **Media library entry** - A media library record is created referencing the cloud-stored file
5. **Cleanup** - Temporary files are automatically cleaned up by Livewire

## Advantages over Traditional Upload

| Feature | Traditional Upload | Direct Upload |
|---------|-------------------|---------------|
| Temporary storage | Required | Minimal (Livewire temporary only) |
| Server load | High (file passes through server) | Low (minimal processing) |
| Scalability | Requires shared storage | Cloud-native, scales easily |
| File size limits | PHP/server limits apply | Only cloud limits apply |
| Network efficiency | Two hops (client→server→cloud) | Optimized (client→temp→cloud) |
| Cloud compatibility | Requires local storage | Fully cloud-compatible |

## Storage Disk Configuration

### AWS S3

Already covered above. Ensure your bucket has proper CORS configuration:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": ["ETag"]
    }
]
```

### DigitalOcean Spaces

```php
's3' => [
    'driver' => 's3',
    'key' => env('DO_SPACES_KEY'),
    'secret' => env('DO_SPACES_SECRET'),
    'region' => env('DO_SPACES_REGION'),
    'bucket' => env('DO_SPACES_BUCKET'),
    'endpoint' => env('DO_SPACES_ENDPOINT'),
],
```

### Other S3-Compatible Services

Any S3-compatible service (Wasabi, MinIO, etc.) can be used with the same configuration pattern.

## Security Considerations

1. **Authentication** - By default, uploads work for both authenticated and guest users
2. **File validation** - Files are validated for size and type on the server
3. **Access control** - Configure S3 bucket permissions appropriately
4. **Rate limiting** - Built-in rate limiting prevents abuse
5. **MIME type checking** - Only allowed file types are accepted

### Require Authentication

To require authentication for uploads:

```php
// config/direct-upload.php
'security' => [
    'require_auth' => true,
],
```

## Troubleshooting

### Files Not Appearing in S3

1. Check AWS credentials in `.env`
2. Verify bucket name and region
3. Check IAM permissions (needs `s3:PutObject`, `s3:GetObject`, `s3:DeleteObject`)
4. Review Laravel logs for errors

### CORS Errors

If you see CORS errors in the browser console:

1. Configure CORS in your S3 bucket (see above)
2. Ensure your application URL is in allowed origins
3. Check that methods include PUT and POST

### Upload Fails Silently

1. Check PHP error logs
2. Verify Livewire is properly installed
3. Check file size against `max_file_size` setting
4. Review browser console for JavaScript errors

### Uploads Are Slow

1. Choose an S3 region close to your users
2. Consider using CloudFront or similar CDN
3. Check your internet connection/upload speed
4. Verify S3 transfer acceleration is enabled if needed

## Comparison with Chunked Upload

| Feature | Direct Upload | Chunked Upload |
|---------|---------------|----------------|
| **Best for** | Cloud-native apps | Large files (>100MB) |
| **File size limit** | ~100MB recommended | 1GB+ |
| **Complexity** | Simple | More complex |
| **Browser support** | Modern browsers | All browsers |
| **Network resilience** | Standard | Resumable |
| **Server requirements** | Minimal | More processing |

**When to use Direct Upload:**
- Cloud-based applications
- Files under 100MB
- Simple upload requirements
- Modern browser users

**When to use Chunked Upload:**
- Very large files (>100MB)
- Poor network conditions requiring resume
- Older browser support needed

## API and Events

### Livewire Events

The component dispatches these events:

- `fileUploaded` - When a file is successfully uploaded (detail: media ID)
- `fileRemoved` - When a file is removed (detail: media ID)

### Component Methods

```blade
{{-- Access the component --}}
@php
    $upload = app(\Laravolt\Media\Livewire\DirectUpload::class);
@endphp

{{-- Available methods --}}
$upload->removeFile($mediaId);  // Remove a file
$upload->uploadedFiles;         // Get array of uploaded files
```

## Advanced Usage

### Custom Styling

The component comes with default styling. To customize:

```blade
<div class="my-custom-upload">
    <livewire:media::direct-upload />
</div>

<style>
    .my-custom-upload .direct-upload-component {
        /* Your custom styles */
    }
</style>
```

### Integration with Forms

```blade
<form wire:submit="save">
    <div class="form-group">
        <label>Document Name</label>
        <input type="text" wire:model="documentName">
    </div>
    
    <div class="form-group">
        <label>Attach Files</label>
        <livewire:media::direct-upload 
            disk="s3"
            collection="documents"
        />
    </div>
    
    <button type="submit">Save</button>
</form>
```

### Programmatic File Removal

```javascript
// Remove file using Livewire
Livewire.dispatch('removeFile', { id: mediaId });

// Or via fetch API
fetch(`/media/${mediaId}`, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
}).then(() => {
    console.log('File removed');
});
```

## Testing

Test the direct upload functionality:

```bash
php artisan test tests/Feature/Media/DirectUploadTest.php
```

## Production Checklist

- [ ] Configure S3 bucket with proper permissions
- [ ] Set up CORS configuration on S3 bucket
- [ ] Configure environment variables in production
- [ ] Test uploads with various file types
- [ ] Verify file size limits work as expected
- [ ] Set up monitoring for S3 usage and costs
- [ ] Configure S3 lifecycle rules for old files
- [ ] Test with actual users in production-like environment
- [ ] Set up error tracking and logging

## Need Help?

- Check Laravel logs: `storage/logs/laravel.log`
- Review Livewire documentation: https://livewire.laravel.com/docs/uploads
- Check AWS S3 documentation for storage configuration
- Enable debug mode to see detailed error messages

## Next Steps

- Explore [chunked uploads](CHUNKED_UPLOAD_GUIDE.md) for very large files
- Set up [media transformations](https://spatie.be/docs/laravel-medialibrary/v11/converting-images/defining-conversions) with Spatie Media Library
- Configure [image optimization](https://spatie.be/docs/laravel-medialibrary/v11/converting-images/optimizing-converted-images)
- Implement [custom media collections](https://spatie.be/docs/laravel-medialibrary/v11/working-with-media-collections/simple-media-collections)
