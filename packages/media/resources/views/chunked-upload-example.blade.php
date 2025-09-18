<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravolt Chunked Upload Example</title>
    
    <!-- Resumable.js -->
    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
    
    <!-- FilePond -->
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-chunk-upload/dist/filepond-plugin-chunk-upload.min.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .upload-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #4CAF50;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }
        
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            transition: border-color 0.3s ease;
        }
        
        .drop-zone.dragover {
            border-color: #4CAF50;
            background-color: #f9f9f9;
        }
        
        .file-list {
            margin: 20px 0;
        }
        
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin: 5px 0;
        }
        
        .file-info {
            flex: 1;
        }
        
        .file-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-uploading {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <h1>Laravolt Chunked Upload Examples</h1>
    
    <!-- Resumable.js Example -->
    <div class="upload-section">
        <h2>Resumable.js Upload</h2>
        <p>Upload large files using Resumable.js with chunked upload support.</p>
        
        <div class="drop-zone" id="drop-zone">
            <p>Drag and drop files here or click to select</p>
            <input type="file" id="file-input" multiple style="display: none;">
            <button class="btn btn-primary" onclick="document.getElementById('file-input').click()">
                Select Files
            </button>
        </div>
        
        <div class="progress-bar" id="progress-container" style="display: none;">
            <div class="progress-fill" id="progress-bar">0%</div>
        </div>
        
        <div class="file-list" id="file-list"></div>
        
        <div>
            <button class="btn btn-primary" id="start-upload" onclick="startResumableUpload()">Start Upload</button>
            <button class="btn btn-secondary" id="pause-upload" onclick="pauseResumableUpload()">Pause</button>
            <button class="btn btn-secondary" id="cancel-upload" onclick="cancelResumableUpload()">Cancel</button>
        </div>
    </div>
    
    <!-- FilePond Example -->
    <div class="upload-section">
        <h2>FilePond Upload</h2>
        <p>Upload files using FilePond with chunked upload support and better UX.</p>
        
        <input type="file" id="filepond-upload" multiple>
        
        <div class="file-list" id="filepond-list"></div>
    </div>
    
    <script>
        // Resumable.js implementation
        let resumableUploader = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Resumable.js uploader
            resumableUploader = new LaravoltChunkedUpload({
                target: '/media/chunk',
                chunkSize: 2 * 1024 * 1024, // 2MB chunks
                onFileAdded: function(file) {
                    addFileToList(file, 'resumable');
                },
                onProgress: function(file, progress) {
                    updateFileProgress(file, progress, 'resumable');
                },
                onSuccess: function(file, response) {
                    updateFileStatus(file, 'success', 'resumable');
                    if (response.success && response.files && response.files.length > 0) {
                        const media = response.files[0];
                        console.log('Media saved:', media);
                    }
                },
                onError: function(file, message) {
                    updateFileStatus(file, 'error', 'resumable');
                    console.error('Upload failed:', message);
                },
                onComplete: function() {
                    console.log('All uploads completed');
                }
            });
            
            // Handle file input change
            const fileInput = document.getElementById('file-input');
            fileInput.addEventListener('change', function(e) {
                resumableUploader.addFiles(e.target.files);
            });
            
            // Handle drag and drop
            const dropZone = document.getElementById('drop-zone');
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });
            
            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropZone.classList.remove('dragover');
            });
            
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                resumableUploader.addFiles(e.dataTransfer.files);
            });
            
            // Initialize FilePond
            initializeFilePond();
        });
        
        function startResumableUpload() {
            if (resumableUploader) {
                resumableUploader.upload();
                document.getElementById('progress-container').style.display = 'block';
            }
        }
        
        function pauseResumableUpload() {
            if (resumableUploader) {
                resumableUploader.pause();
            }
        }
        
        function cancelResumableUpload() {
            if (resumableUploader) {
                resumableUploader.cancel();
                document.getElementById('progress-container').style.display = 'none';
            }
        }
        
        function addFileToList(file, type) {
            const fileList = document.getElementById(type === 'resumable' ? 'file-list' : 'filepond-list');
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.id = `file-${type}-${file.uniqueIdentifier || file.id}`;
            
            fileItem.innerHTML = `
                <div class="file-info">
                    <strong>${file.fileName || file.filename}</strong>
                    <br>
                    <small>Size: ${formatFileSize(file.size)}</small>
                </div>
                <div class="file-status status-uploading">Uploading...</div>
            `;
            
            fileList.appendChild(fileItem);
        }
        
        function updateFileProgress(file, progress, type) {
            const fileItem = document.getElementById(`file-${type}-${file.uniqueIdentifier || file.id}`);
            if (fileItem) {
                const status = fileItem.querySelector('.file-status');
                status.textContent = `${progress}%`;
                status.className = 'file-status status-uploading';
            }
            
            // Update global progress bar for Resumable.js
            if (type === 'resumable') {
                const progressBar = document.getElementById('progress-bar');
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
            }
        }
        
        function updateFileStatus(file, status, type) {
            const fileItem = document.getElementById(`file-${type}-${file.uniqueIdentifier || file.id}`);
            if (fileItem) {
                const statusElement = fileItem.querySelector('.file-status');
                statusElement.className = `file-status status-${status}`;
                
                switch (status) {
                    case 'success':
                        statusElement.textContent = 'Uploaded';
                        break;
                    case 'error':
                        statusElement.textContent = 'Failed';
                        break;
                    default:
                        statusElement.textContent = status;
                }
            }
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // FilePond implementation
        function initializeFilePond() {
            // Register FilePond plugins
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginChunkUpload
            );
            
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
                            try {
                                const data = JSON.parse(response);
                                if (data.success) {
                                    return data.chunk || 'chunk-uploaded';
                                } else {
                                    throw new Error(data.message || 'Upload failed');
                                }
                            } catch (e) {
                                throw new Error('Invalid response format');
                            }
                        },
                        onerror: (response) => {
                            throw new Error('Upload failed');
                        }
                    }
                },
                
                onaddfile: (error, file) => {
                    if (error) {
                        console.error('Error adding file:', error);
                        return;
                    }
                    addFileToList(file, 'filepond');
                },
                
                onprocessfile: (error, file) => {
                    if (error) {
                        console.error('Error processing file:', error);
                        updateFileStatus(file, 'error', 'filepond');
                        return;
                    }
                    updateFileStatus(file, 'success', 'filepond');
                }
            });
        }
    </script>
</body>
</html>