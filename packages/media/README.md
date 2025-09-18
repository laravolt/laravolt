# Laravolt Media Package

Package media management untuk Laravolt Platform dengan dukungan upload file besar melalui chunked upload.

## Fitur

- ✅ **Multiple Media Handlers**: Redactor, Fileuploader, dan Chunked
- ✅ **Chunked Upload**: Upload file besar tanpa mengubah konfigurasi server
- ✅ **Spatie Media Library Integration**: Integrasi penuh dengan Spatie Media Library
- ✅ **Guest Upload Support**: Dukungan upload untuk user tidak terautentikasi
- ✅ **Frontend Options**: Resumable.js dan FilePond
- ✅ **Automatic Cleanup**: Cleanup otomatis chunk sementara
- ✅ **Configurable**: Konfigurasi lengkap untuk berbagai kebutuhan

## Instalasi

### 1. Install Dependencies

```bash
composer require laravolt/laravolt
composer require pion/laravel-chunk-upload
```

### 2. Publish Konfigurasi (Opsional)

```bash
php artisan vendor:publish --tag=chunked-upload-config
```

### 3. Setup Cleanup Job (Opsional)

Tambahkan ke `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Cleanup chunk files older than 24 hours
    $schedule->job(new \Laravolt\Media\Jobs\CleanupChunksJob(24))->daily();
}
```

## Penggunaan

### Media Handlers

Package ini menyediakan tiga media handler:

#### 1. RedactorMediaHandler (Default)
Untuk editor Redactor, mendukung multiple file upload.

```php
// Endpoint: POST /media/media
// Parameters: handler=redactor, file[]=files
```

#### 2. FileuploaderMediaHandler
Untuk fileuploader.js, mendukung single file upload dengan progress.

```php
// Endpoint: POST /media/media
// Parameters: handler=fileuploader, _key=file, _action=upload
```

#### 3. ChunkedMediaHandler (New!)
Untuk upload file besar dengan chunking.

```php
// Endpoint: POST /media/chunk
// Parameters: handler=chunked, _action=upload, file=chunk
```

### Chunked Upload

#### Backend API

**Upload Chunk:**
```http
POST /media/chunk
Content-Type: multipart/form-data

file: [chunk file]
handler: chunked
_action: upload
```

**Complete Upload:**
```http
POST /media/chunk/complete
Content-Type: application/json

{
    "handler": "chunked",
    "_action": "complete",
    "file_id": "unique-file-id",
    "file_name": "original-filename.ext"
}
```

**Delete Media:**
```http
POST /media/chunk
Content-Type: application/json

{
    "handler": "chunked",
    "_action": "delete",
    "id": 123
}
```

#### Frontend Implementation

**Resumable.js:**
```javascript
const uploader = new Resumable({
    target: '/media/chunk',
    chunkSize: 2 * 1024 * 1024, // 2MB chunks
    query: {
        handler: 'chunked',
        _action: 'upload'
    }
});

uploader.on('fileAdded', function(file) {
    uploader.upload();
});
```

**FilePond:**
```javascript
FilePond.registerPlugin(FilePondPluginChunkUpload);

const pond = FilePond.create(document.getElementById('filepond-upload'), {
    chunkUploads: true,
    chunkSize: 2 * 1024 * 1024,
    server: {
        url: '/media',
        process: {
            url: '/chunk',
            method: 'POST'
        }
    }
});
```

## Konfigurasi

### Chunked Upload Configuration

File: `config/chunked-upload.php`

```php
return [
    'chunk_size' => 2 * 1024 * 1024, // 2MB
    'max_file_size' => 100 * 1024 * 1024, // 100MB
    'allowed_types' => [
        'image/jpeg',
        'image/png',
        'video/mp4',
        'application/pdf'
    ],
    'cleanup' => [
        'enabled' => true,
        'max_age_hours' => 24
    ]
];
```

### Environment Variables

```env
# Chunked Upload Configuration
CHUNKED_UPLOAD_CHUNK_SIZE=2097152
CHUNKED_UPLOAD_MAX_FILE_SIZE=104857600
CHUNKED_UPLOAD_CLEANUP_ENABLED=true
CHUNKED_UPLOAD_CLEANUP_MAX_AGE=24
```

## Commands

### Cleanup Chunks

```bash
# Cleanup chunks older than 24 hours
php artisan media:cleanup-chunks

# Cleanup chunks older than 48 hours
php artisan media:cleanup-chunks --max-age=48

# Dry run (show what would be deleted)
php artisan media:cleanup-chunks --dry-run
```

## Testing

```bash
# Run all tests
php artisan test packages/media/tests/

# Run specific test
php artisan test packages/media/tests/ChunkedMediaHandlerTest.php
```

## Examples

Lihat file berikut untuk contoh implementasi:

- `resources/views/chunked-upload-example.blade.php` - Contoh HTML lengkap
- `resources/js/chunked-upload-resumable.js` - Implementasi Resumable.js
- `resources/js/chunked-upload-filepond.js` - Implementasi FilePond
- `examples/ChunkedUploadIntegration.php` - Contoh integrasi Laravel

## Troubleshooting

### Error 413 Payload Too Large

1. Pastikan chunk size tidak terlalu besar (maksimal 2MB)
2. Periksa konfigurasi nginx/Apache
3. Pastikan `upload_max_filesize` dan `post_max_size` di PHP cukup besar

### Chunk Tidak Tersimpan

1. Pastikan direktori `storage/app/chunks` dapat ditulis
2. Periksa permission direktori storage
3. Pastikan disk `local` tersedia

### Upload Terputus

1. Pastikan `testChunks: true` di Resumable.js
2. Implementasikan retry logic di frontend
3. Periksa network timeout

## Migration Guide

### Dari Handler Lama

1. **Ganti Endpoint:**
   ```javascript
   // Lama
   target: '/media/media'
   
   // Baru
   target: '/media/chunk'
   ```

2. **Tambahkan Parameter:**
   ```javascript
   query: {
       handler: 'chunked',
       _action: 'upload'
   }
   ```

3. **Update Error Handling:**
   ```javascript
   // Response format tetap sama
   {
       "success": true,
       "files": [...]
   }
   ```

### Upgrade dari Laravolt Lama

1. Install dependency baru:
   ```bash
   composer require pion/laravel-chunk-upload
   ```

2. Publish konfigurasi:
   ```bash
   php artisan vendor:publish --tag=chunked-upload-config
   ```

3. Update frontend untuk menggunakan chunked upload

## Security

- ✅ CSRF protection otomatis
- ✅ Validasi file type dan size
- ✅ Rate limiting (opsional)
- ✅ Cleanup otomatis chunk sementara
- ✅ Dukungan Guest upload yang aman

## Performance

- ✅ Upload paralel dengan `simultaneousUploads`
- ✅ Resume upload yang terputus
- ✅ Progress tracking real-time
- ✅ Cleanup otomatis untuk menghemat storage

## Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

MIT License. See LICENSE file for details.

## Support

Untuk dukungan dan pertanyaan:
- GitHub Issues: [Laravolt Repository](https://github.com/laravolt/laravolt)
- Documentation: [Laravolt Docs](https://laravolt.dev)