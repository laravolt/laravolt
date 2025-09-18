# Chunked Upload Installation Guide

This guide will help you set up chunked upload functionality in your Laravolt application.

## Quick Setup (5 minutes)

### 1. Install Dependencies

The `pion/laravel-chunk-upload` dependency should already be added to your `composer.json`. If not, add it:

```bash
composer require pion/laravel-chunk-upload
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=laravolt-media-config
```

This creates `config/chunked-upload.php` where you can customize settings.

### 3. Publish Assets

```bash
php artisan vendor:publish --tag=laravolt-media-assets
```

This publishes the JavaScript component to `public/js/components/chunked-uploader.js`.

### 4. Set Up Cleanup (Recommended)

Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('media:cleanup-chunks')->dailyAt('02:00');
}
```

### 5. Create Your First Chunked Upload

Create a simple HTML page:

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chunked Upload Test</title>
</head>
<body>
    <div id="upload-zone" style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
        <p>Drop files here or <button id="browse">Browse</button></p>
        <div id="progress"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
    <script src="{{ asset('js/components/chunked-uploader.js') }}"></script>
    
    <script>
    const uploader = new ChunkedUploader(document.getElementById('upload-zone'), {
        onFileSuccess: function(file, response) {
            document.getElementById('progress').innerHTML = 
                `<p>✅ ${file.fileName} uploaded successfully!</p>`;
        },
        onFileProgress: function(file, progress) {
            document.getElementById('progress').innerHTML = 
                `<p>Uploading ${file.fileName}: ${Math.round(progress * 100)}%</p>`;
        }
    });
    </script>
</body>
</html>
```

## Testing Your Setup

1. Create a large file (> 10MB) to test chunking
2. Upload it using your test page
3. Check that it appears in your media library
4. Verify chunks are cleaned up after 24 hours

## Common Configuration Changes

### Increase Chunk Size for Better Performance

```php
// config/chunked-upload.php
'default_chunk_size' => 5 * 1024 * 1024, // 5MB chunks
```

### Allow Different File Types

```php
// config/chunked-upload.php
'allowed_mime_types' => [
    'image/*',
    'application/pdf',
    'video/mp4',
    // Add your types here
],
```

### Increase Maximum File Size

```php
// config/chunked-upload.php
'max_file_size' => 500 * 1024 * 1024, // 500MB
```

## Troubleshooting

### Uploads Fail with 413 Error
- Check your chunk size (should be small, e.g., 1-5MB)
- Verify nginx/Apache configuration allows chunk size

### Chunks Don't Assemble
- Check storage permissions
- Verify `storage/app/chunks` directory is writable
- Check server logs for errors

### Files Don't Appear in Media Library
- Ensure Spatie Media Library is properly configured
- Check that Guest model exists if using anonymous uploads
- Verify database permissions

## Next Steps

- Read the full [README.md](README.md) for advanced configuration
- Check out [chunked-upload-examples.blade.php](resources/views/media/chunked-upload-examples.blade.php) for more examples
- Set up automated testing with the provided test files

## Need Help?

- Check the logs: `storage/logs/laravel.log`
- Run the cleanup command manually: `php artisan media:cleanup-chunks`
- Enable debug mode in your handler for detailed logging

## Production Checklist

- [ ] Configure appropriate chunk sizes for your use case
- [ ] Set up automated cleanup scheduling
- [ ] Configure file type restrictions
- [ ] Set reasonable file size limits
- [ ] Test with your expected file sizes
- [ ] Monitor disk space usage
- [ ] Set up proper error handling and user feedback