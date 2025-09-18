# Laravolt Chunked Upload

Dukungan chunked upload untuk Laravolt Media Package yang memungkinkan upload file besar tanpa mengubah konfigurasi PHP/nginx.

## Fitur

- ✅ Upload file besar dengan chunking (pemecahan file)
- ✅ Resume upload yang terputus
- ✅ Dukungan untuk user terautentikasi dan Guest
- ✅ Integrasi dengan Spatie Media Library
- ✅ Dua opsi frontend: Resumable.js dan FilePond
- ✅ Cleanup otomatis chunk sementara
- ✅ Response format konsisten dengan handler lainnya
- ✅ Backward compatible dengan handler yang ada

## Instalasi

### 1. Install Dependency

Tambahkan dependency ke `composer.json`:

```bash
composer require pion/laravel-chunk-upload
```

### 2. Publish Konfigurasi (Opsional)

```bash
php artisan vendor:publish --provider="Pion\Laravel\ChunkUpload\Providers\ChunkUploadServiceProvider"
```

### 3. Setup Cleanup Job (Opsional)

Tambahkan ke `app/Console/Kernel.php` untuk cleanup otomatis:

```php
protected function schedule(Schedule $schedule)
{
    // Cleanup chunk files older than 24 hours
    $schedule->job(new \Laravolt\Media\Jobs\CleanupChunksJob(24))->daily();
}
```

## Penggunaan

### Backend API

#### Endpoint Upload Chunk

```http
POST /media/chunk
Content-Type: multipart/form-data

file: [chunk file]
handler: chunked
_action: upload
```

**Response:**
```json
{
    "success": true,
    "done": 50,
    "chunk": 5,
    "total_chunks": 10
}
```

#### Endpoint Complete Upload

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

**Response:**
```json
{
    "success": true,
    "files": [
        {
            "file": "https://example.com/media/123/filename.ext",
            "name": "filename.ext",
            "size": 1048576,
            "type": "image/jpeg",
            "data": {
                "id": 123,
                "url": "https://example.com/media/123/filename.ext",
                "thumbnail": "https://example.com/media/123/filename.ext"
            }
        }
    ]
}
```

#### Endpoint Delete Media

```http
POST /media/chunk
Content-Type: application/json

{
    "handler": "chunked",
    "_action": "delete",
    "id": 123
}
```

### Frontend Implementation

#### 1. Resumable.js

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
</head>
<body>
    <div id="drop-zone">
        <p>Drag and drop files here</p>
        <input type="file" id="file-input" multiple>
    </div>
    
    <div id="progress-bar" style="width: 100%; height: 20px; background: #f0f0f0;">
        <div id="progress-fill" style="width: 0%; height: 100%; background: #4CAF50;"></div>
    </div>
    
    <script>
        const uploader = new Resumable({
            target: '/media/chunk',
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            simultaneousUploads: 3,
            testChunks: true,
            query: {
                handler: 'chunked',
                _action: 'upload'
            },
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        uploader.on('fileAdded', function(file) {
            console.log('File added:', file.fileName);
            uploader.upload();
        });
        
        uploader.on('fileProgress', function(file) {
            const progress = Math.floor(file.progress() * 100);
            document.getElementById('progress-fill').style.width = progress + '%';
        });
        
        uploader.on('fileSuccess', function(file, response) {
            console.log('Upload successful:', response);
            const data = JSON.parse(response);
            if (data.success && data.files) {
                console.log('Media saved:', data.files[0]);
            }
        });
        
        uploader.on('fileError', function(file, message) {
            console.error('Upload failed:', message);
        });
        
        // Handle file input
        document.getElementById('file-input').addEventListener('change', function(e) {
            uploader.addFiles(e.target.files);
        });
        
        // Handle drag and drop
        const dropZone = document.getElementById('drop-zone');
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            uploader.addFiles(e.dataTransfer.files);
        });
    </script>
</body>
</html>
```

#### 2. FilePond

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-chunk-upload/dist/filepond-plugin-chunk-upload.min.js"></script>
</head>
<body>
    <input type="file" id="filepond-upload" multiple>
    
    <script>
        // Register FilePond plugin
        FilePond.registerPlugin(FilePondPluginChunkUpload);
        
        // Create FilePond instance
        const pond = FilePond.create(document.getElementById('filepond-upload'), {
            chunkUploads: true,
            chunkForce: true,
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            chunkRetryDelays: [0, 1000, 3000, 5000],
            maxFileSize: '100MB',
            acceptedFileTypes: ['image/*', 'video/*', 'application/pdf'],
            
            server: {
                url: '/media',
                process: {
                    url: '/chunk',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    onload: (response) => {
                        const data = JSON.parse(response);
                        if (data.success) {
                            return data.chunk || 'chunk-uploaded';
                        } else {
                            throw new Error(data.message || 'Upload failed');
                        }
                    }
                }
            },
            
            onaddfile: (error, file) => {
                if (error) {
                    console.error('Error adding file:', error);
                    return;
                }
                console.log('File added:', file.filename);
            },
            
            onprocessfile: (error, file) => {
                if (error) {
                    console.error('Error processing file:', error);
                    return;
                }
                console.log('File processed successfully:', file.filename);
            }
        });
    </script>
</body>
</html>
```

## Konfigurasi

### Chunk Size

Rekomendasi chunk size:
- **1-2MB**: Aman untuk sebagian besar server
- **5MB**: Untuk server dengan konfigurasi yang lebih baik
- **10MB+**: Hanya untuk server dengan limit yang tinggi

### Validasi File

```php
// Di ChunkedMediaHandler, tambahkan validasi sesuai kebutuhan
protected function validateFile($file)
{
    // Validasi ukuran total
    $maxSize = 100 * 1024 * 1024; // 100MB
    if ($file->getSize() > $maxSize) {
        throw new \Exception('File too large');
    }
    
    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/png', 'video/mp4'];
    if (!in_array($file->getMimeType(), $allowedTypes)) {
        throw new \Exception('File type not allowed');
    }
}
```

### Rate Limiting

Tambahkan rate limiting untuk endpoint chunk:

```php
// Di routes/web.php
Route::post('chunk', [MediaController::class, 'store'])
    ->name('chunk.upload')
    ->middleware('throttle:60,1') // 60 requests per minute
    ->withoutMiddleware('auth');
```

## Troubleshooting

### Error 413 Payload Too Large

Jika masih mendapat error 413:
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

## Testing

Jalankan test untuk memastikan chunked upload berfungsi:

```bash
php artisan test packages/media/tests/ChunkedMediaHandlerTest.php
```

## Migration dari Handler Lama

Untuk migrasi dari `FileuploaderMediaHandler` atau `RedactorMediaHandler`:

1. Ganti endpoint dari `/media/media` ke `/media/chunk`
2. Tambahkan parameter `handler: 'chunked'`
3. Implementasikan chunking di frontend
4. Update error handling untuk response format yang sama

## Keamanan

- ✅ CSRF protection otomatis
- ✅ Validasi file type dan size
- ✅ Rate limiting (opsional)
- ✅ Cleanup otomatis chunk sementara
- ✅ Dukungan Guest upload yang aman

## Performa

- ✅ Upload paralel dengan `simultaneousUploads`
- ✅ Resume upload yang terputus
- ✅ Progress tracking real-time
- ✅ Cleanup otomatis untuk menghemat storage

## Contoh Lengkap

Lihat file `resources/views/chunked-upload-example.blade.php` untuk contoh implementasi lengkap dengan kedua opsi frontend.