/**
 * Laravolt Chunked Upload with Resumable.js
 * 
 * Example implementation for chunked file upload using Resumable.js
 * Compatible with Laravolt's ChunkedMediaHandler
 */

class LaravoltChunkedUpload {
    constructor(options = {}) {
        this.options = {
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            simultaneousUploads: 3,
            testChunks: true,
            throttleProgressCallbacks: 1,
            method: 'POST',
            uploadMethod: 'POST',
            ...options
        };

        this.resumable = null;
        this.initializeResumable();
    }

    initializeResumable() {
        this.resumable = new Resumable({
            target: this.options.target || '/media/chunk',
            chunkSize: this.options.chunkSize,
            simultaneousUploads: this.options.simultaneousUploads,
            testChunks: this.options.testChunks,
            throttleProgressCallbacks: this.options.throttleProgressCallbacks,
            method: this.options.method,
            uploadMethod: this.options.uploadMethod,
            query: {
                handler: 'chunked',
                _action: 'upload'
            },
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken()
            }
        });

        this.setupEventHandlers();
    }

    setupEventHandlers() {
        // File added
        this.resumable.on('fileAdded', (file) => {
            console.log('File added:', file.fileName);
            if (this.options.onFileAdded) {
                this.options.onFileAdded(file);
            }
        });

        // File progress
        this.resumable.on('fileProgress', (file) => {
            const progress = Math.floor(file.progress() * 100);
            console.log('Upload progress:', progress + '%');
            if (this.options.onProgress) {
                this.options.onProgress(file, progress);
            }
        });

        // File success
        this.resumable.on('fileSuccess', (file, response) => {
            console.log('File uploaded successfully:', file.fileName);
            try {
                const data = JSON.parse(response);
                if (this.options.onSuccess) {
                    this.options.onSuccess(file, data);
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                if (this.options.onError) {
                    this.options.onError(file, 'Invalid response format');
                }
            }
        });

        // File error
        this.resumable.on('fileError', (file, message) => {
            console.error('Upload error:', message);
            if (this.options.onError) {
                this.options.onError(file, message);
            }
        });

        // Upload complete
        this.resumable.on('complete', () => {
            console.log('All files uploaded');
            if (this.options.onComplete) {
                this.options.onComplete();
            }
        });
    }

    /**
     * Add files to upload queue
     */
    addFiles(files) {
        this.resumable.addFiles(files);
    }

    /**
     * Start upload
     */
    upload() {
        this.resumable.upload();
    }

    /**
     * Pause upload
     */
    pause() {
        this.resumable.pause();
    }

    /**
     * Resume upload
     */
    resume() {
        this.resumable.upload();
    }

    /**
     * Cancel upload
     */
    cancel() {
        this.resumable.cancel();
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }
}

// Example usage with HTML form
document.addEventListener('DOMContentLoaded', function() {
    const uploader = new LaravoltChunkedUpload({
        target: '/media/chunk',
        onFileAdded: function(file) {
            console.log('File added:', file.fileName);
            // Update UI to show file is ready for upload
        },
        onProgress: function(file, progress) {
            console.log('Progress:', progress + '%');
            // Update progress bar
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                progressBar.style.width = progress + '%';
                progressBar.textContent = progress + '%';
            }
        },
        onSuccess: function(file, response) {
            console.log('Upload successful:', response);
            // Handle successful upload
            if (response.success && response.files && response.files.length > 0) {
                const media = response.files[0];
                console.log('Media saved:', media);
                // Update UI with uploaded media info
            }
        },
        onError: function(file, message) {
            console.error('Upload failed:', message);
            // Show error message to user
        },
        onComplete: function() {
            console.log('All uploads completed');
            // Reset UI or show completion message
        }
    });

    // Handle file input change
    const fileInput = document.getElementById('file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            uploader.addFiles(e.target.files);
            uploader.upload();
        });
    }

    // Handle drag and drop
    const dropZone = document.getElementById('drop-zone');
    if (dropZone) {
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
            uploader.addFiles(e.dataTransfer.files);
            uploader.upload();
        });
    }
});