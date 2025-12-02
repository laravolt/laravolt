/**
 * Client-Side Uploader for Laravolt
 * Supports direct uploads to S3/R2 using presigned URLs
 * Includes multipart upload support for large files
 */

class ClientSideUploader {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            // Configuration endpoint
            configEndpoint: '/media/client-upload/config',
            // Upload endpoints
            initiateEndpoint: '/media/client-upload/initiate',
            presignPartEndpoint: '/media/client-upload/presign-part',
            presignPartsEndpoint: '/media/client-upload/presign-parts',
            completeMultipartEndpoint: '/media/client-upload/complete-multipart',
            completeSimpleEndpoint: '/media/client-upload/complete-simple',
            abortEndpoint: '/media/client-upload/abort',
            // Upload settings (will be overridden by server config)
            maxFileSize: 5 * 1024 * 1024 * 1024, // 5GB
            multipartThreshold: 100 * 1024 * 1024, // 100MB
            multipartChunkSize: 10 * 1024 * 1024, // 10MB
            maxConcurrentUploads: 4,
            retryAttempts: 3,
            retryDelay: 1000,
            // Callbacks
            onFileAdded: null,
            onUploadStart: null,
            onProgress: null,
            onPartComplete: null,
            onFileSuccess: null,
            onFileError: null,
            onComplete: null,
            onAbort: null,
            // File validation
            allowedMimeTypes: null,
            allowedExtensions: null,
            maxFiles: null,
            // CSRF token (can be passed explicitly)
            csrfToken: null,
            ...options
        };

        this.uploads = new Map();
        this.config = null;
        this.initialized = false;

        this.init();
    }

    async init() {
        try {
            // Fetch configuration from server
            await this.fetchConfig();
            this.setupElement();
            this.initialized = true;
        } catch (error) {
            console.error('ClientSideUploader: Failed to initialize', error);
        }
    }

    async fetchConfig() {
        try {
            const csrfToken = this.getCsrfToken();
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            // Add CSRF token if available (GET requests typically don't need it, but add for consistency)
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await fetch(this.options.configEndpoint, {
                headers: headers,
                credentials: 'same-origin' // Include cookies for session
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.config) {
                    this.config = data.config;
                    // Override options with server config
                    if (this.config.maxFileSize) this.options.maxFileSize = this.config.maxFileSize;
                    if (this.config.multipartThreshold) this.options.multipartThreshold = this.config.multipartThreshold;
                    if (this.config.multipartChunkSize) this.options.multipartChunkSize = this.config.multipartChunkSize;
                    if (this.config.maxConcurrentUploads) this.options.maxConcurrentUploads = this.config.maxConcurrentUploads;
                    if (this.config.allowedMimeTypes) this.options.allowedMimeTypes = this.config.allowedMimeTypes;
                    if (this.config.allowedExtensions) this.options.allowedExtensions = this.config.allowedExtensions;
                    if (this.config.endpoints) {
                        this.options.initiateEndpoint = this.config.endpoints.initiate || this.options.initiateEndpoint;
                        this.options.completeSimpleEndpoint = this.config.endpoints.complete || this.options.completeSimpleEndpoint;
                    }
                }
            }
        } catch (error) {
            console.warn('ClientSideUploader: Could not fetch config, using defaults', error);
        }
    }

    setupElement() {
        // Create hidden file input
        this.fileInput = document.createElement('input');
        this.fileInput.type = 'file';
        this.fileInput.multiple = this.options.maxFiles !== 1;
        this.fileInput.style.display = 'none';
        this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        this.element.appendChild(this.fileInput);

        // Setup drag and drop
        if (this.element.classList.contains('file-drop-zone')) {
            this.setupDragAndDrop();
        }

        // Setup browse button
        const browseButton = this.element.querySelector('.file-browse-button') || this.element;
        browseButton.addEventListener('click', (e) => {
            if (e.target !== this.fileInput) {
                this.fileInput.click();
            }
        });
    }

    setupDragAndDrop() {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.element.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            this.element.addEventListener(eventName, () => {
                this.element.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.element.addEventListener(eventName, () => {
                this.element.classList.remove('drag-over');
            });
        });

        this.element.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            this.addFiles(Array.from(files));
        });
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.addFiles(files);
        // Reset input so the same file can be selected again
        e.target.value = '';
    }

    addFiles(files) {
        files.forEach(file => {
            const validation = this.validateFile(file);
            if (validation.valid) {
                this.addFile(file);
            } else {
                if (this.options.onFileError) {
                    this.options.onFileError({ file, fileName: file.name }, validation.error);
                }
            }
        });
    }

    validateFile(file) {
        // Check file size
        if (file.size > this.options.maxFileSize) {
            return {
                valid: false,
                error: `File size exceeds maximum allowed size of ${this.formatBytes(this.options.maxFileSize)}`
            };
        }

        // Check MIME type
        if (this.options.allowedMimeTypes && this.options.allowedMimeTypes.length > 0) {
            if (!this.options.allowedMimeTypes.includes(file.type)) {
                return {
                    valid: false,
                    error: `File type ${file.type} is not allowed`
                };
            }
        }

        // Check extension
        if (this.options.allowedExtensions && this.options.allowedExtensions.length > 0) {
            const extension = file.name.split('.').pop().toLowerCase();
            if (!this.options.allowedExtensions.includes(extension)) {
                return {
                    valid: false,
                    error: `File extension .${extension} is not allowed`
                };
            }
        }

        // Check max files
        if (this.options.maxFiles && this.uploads.size >= this.options.maxFiles) {
            return {
                valid: false,
                error: `Maximum number of files (${this.options.maxFiles}) reached`
            };
        }

        return { valid: true };
    }

    addFile(file) {
        const uploadId = this.generateId();
        const upload = {
            id: uploadId,
            file: file,
            fileName: file.name,
            fileSize: file.size,
            contentType: file.type || 'application/octet-stream',
            status: 'pending',
            progress: 0,
            key: null,
            uploadToken: null,
            multipartUploadId: null,
            parts: [],
            completedParts: [],
            error: null
        };

        this.uploads.set(uploadId, upload);

        if (this.options.onFileAdded) {
            this.options.onFileAdded(upload);
        }

        // Auto-start upload
        this.startUpload(uploadId);

        return uploadId;
    }

    async startUpload(uploadId) {
        const upload = this.uploads.get(uploadId);
        if (!upload || upload.status !== 'pending') return;

        upload.status = 'initiating';

        if (this.options.onUploadStart) {
            this.options.onUploadStart(upload);
        }

        try {
            // Initiate upload
            const initResponse = await this.initiateUpload(upload);

            if (!initResponse.success) {
                throw new Error(initResponse.message || 'Failed to initiate upload');
            }

            upload.key = initResponse.key;
            upload.uploadToken = initResponse.upload_token;

            if (initResponse.type === 'multipart') {
                upload.multipartUploadId = initResponse.upload_id;
                upload.totalParts = initResponse.total_parts;
                upload.chunkSize = initResponse.chunk_size;
                await this.performMultipartUpload(upload);
            } else {
                await this.performSimpleUpload(upload, initResponse.upload_url);
            }
        } catch (error) {
            upload.status = 'error';
            upload.error = error.message;

            if (this.options.onFileError) {
                this.options.onFileError(upload, error.message);
            }
        }
    }

    async initiateUpload(upload) {
        const csrfToken = this.getCsrfToken();
        if (!csrfToken) {
            console.warn('ClientSideUploader: CSRF token not found. Request may fail.');
        }

        const response = await fetch(this.options.initiateEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin', // Include cookies for session
            body: JSON.stringify({
                filename: upload.fileName,
                content_type: upload.contentType,
                file_size: upload.fileSize
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('ClientSideUploader: Initiate upload failed', response.status, errorText);
            throw new Error(`Server error: ${response.status} - ${errorText}`);
        }

        return await response.json();
    }

    async performSimpleUpload(upload, uploadUrl) {
        upload.status = 'uploading';

        try {
            // Upload directly to S3/R2
            const xhr = new XMLHttpRequest();

            await new Promise((resolve, reject) => {
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        upload.progress = e.loaded / e.total;
                        if (this.options.onProgress) {
                            this.options.onProgress(upload, upload.progress);
                        }
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve();
                    } else {
                        reject(new Error(`Upload failed with status ${xhr.status}`));
                    }
                });

                xhr.addEventListener('error', () => {
                    reject(new Error('Network error during upload'));
                });

                xhr.addEventListener('abort', () => {
                    reject(new Error('Upload aborted'));
                });

                xhr.open('PUT', uploadUrl);
                xhr.setRequestHeader('Content-Type', upload.contentType);
                xhr.send(upload.file);

                // Store xhr for potential abort
                upload.xhr = xhr;
            });

            // Complete the upload
            await this.completeSimpleUpload(upload);
        } catch (error) {
            throw error;
        }
    }

    async completeSimpleUpload(upload) {
        const response = await fetch(this.options.completeSimpleEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                key: upload.key,
                upload_token: upload.uploadToken
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('ClientSideUploader: Complete upload failed', response.status, errorText);
            throw new Error(`Server error: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Failed to complete upload');
        }

        upload.status = 'complete';
        upload.progress = 1;
        upload.result = result;

        if (this.options.onFileSuccess) {
            this.options.onFileSuccess(upload, result);
        }

        this.checkAllComplete();
    }

    async performMultipartUpload(upload) {
        upload.status = 'uploading';

        try {
            const file = upload.file;
            const chunkSize = upload.chunkSize;
            const totalParts = upload.totalParts;

            // Prepare parts
            for (let i = 0; i < totalParts; i++) {
                const start = i * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                upload.parts.push({
                    partNumber: i + 1,
                    start: start,
                    end: end,
                    size: end - start,
                    status: 'pending',
                    etag: null
                });
            }

            // Upload parts with concurrency control
            await this.uploadPartsWithConcurrency(upload);

            // Complete multipart upload
            await this.completeMultipartUpload(upload);
        } catch (error) {
            // Abort the multipart upload on error
            if (upload.multipartUploadId) {
                await this.abortUpload(upload);
            }
            throw error;
        }
    }

    async uploadPartsWithConcurrency(upload) {
        const maxConcurrent = this.options.maxConcurrentUploads;
        const parts = upload.parts;
        let currentIndex = 0;
        let completedCount = 0;
        const totalParts = parts.length;

        return new Promise((resolve, reject) => {
            const uploadNext = async () => {
                if (upload.status === 'aborted') {
                    reject(new Error('Upload aborted'));
                    return;
                }

                if (currentIndex >= totalParts) {
                    if (completedCount === totalParts) {
                        resolve();
                    }
                    return;
                }

                const partIndex = currentIndex++;
                const part = parts[partIndex];

                try {
                    await this.uploadPart(upload, part);
                    completedCount++;

                    // Update overall progress
                    upload.progress = completedCount / totalParts;
                    if (this.options.onProgress) {
                        this.options.onProgress(upload, upload.progress);
                    }

                    if (this.options.onPartComplete) {
                        this.options.onPartComplete(upload, part);
                    }

                    // Start next part
                    uploadNext();
                } catch (error) {
                    reject(error);
                }
            };

            // Start initial concurrent uploads
            const initialCount = Math.min(maxConcurrent, totalParts);
            for (let i = 0; i < initialCount; i++) {
                uploadNext();
            }
        });
    }

    async uploadPart(upload, part, attempt = 1) {
        try {
            // Get presigned URL for this part
            const presignResponse = await fetch(this.options.presignPartEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    key: upload.key,
                    upload_id: upload.multipartUploadId,
                    part_number: part.partNumber
                })
            });

            if (!presignResponse.ok) {
                throw new Error(`Failed to get presigned URL: ${presignResponse.status}`);
            }

            const presignData = await presignResponse.json();

            if (!presignData.success) {
                throw new Error(presignData.message || 'Failed to get presigned URL');
            }

            // Upload the part
            const blob = upload.file.slice(part.start, part.end);

            const response = await fetch(presignData.upload_url, {
                method: 'PUT',
                body: blob
            });

            if (!response.ok) {
                throw new Error(`Part upload failed with status ${response.status}`);
            }

            // Get ETag from response
            const etag = response.headers.get('ETag');
            if (!etag) {
                throw new Error('No ETag in response');
            }

            part.etag = etag.replace(/"/g, '');
            part.status = 'complete';
            upload.completedParts.push({
                part_number: part.partNumber,
                etag: part.etag
            });
        } catch (error) {
            if (attempt < this.options.retryAttempts) {
                // Retry with exponential backoff
                await this.sleep(this.options.retryDelay * Math.pow(2, attempt - 1));
                return this.uploadPart(upload, part, attempt + 1);
            }
            throw error;
        }
    }

    async completeMultipartUpload(upload) {
        const response = await fetch(this.options.completeMultipartEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                key: upload.key,
                upload_id: upload.multipartUploadId,
                upload_token: upload.uploadToken,
                parts: upload.completedParts
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('ClientSideUploader: Complete multipart failed', response.status, errorText);
            throw new Error(`Server error: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Failed to complete multipart upload');
        }

        upload.status = 'complete';
        upload.progress = 1;
        upload.result = result;

        if (this.options.onFileSuccess) {
            this.options.onFileSuccess(upload, result);
        }

        this.checkAllComplete();
    }

    async abortUpload(upload) {
        // For multipart uploads, abort on server
        if (upload.multipartUploadId) {
            try {
                await fetch(this.options.abortEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        key: upload.key,
                        upload_id: upload.multipartUploadId,
                        upload_token: upload.uploadToken // Include token to cleanup media record
                    })
                });
            } catch (error) {
                console.error('Failed to abort upload', error);
            }
        } else if (upload.uploadToken) {
            // For simple uploads that were initiated but not completed, cleanup media record
            try {
                await fetch(this.options.abortEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        key: upload.key || '',
                        upload_id: '', // Empty for simple uploads
                        upload_token: upload.uploadToken
                    })
                });
            } catch (error) {
                console.error('Failed to cleanup aborted simple upload', error);
            }
        }

        upload.status = 'aborted';

        if (this.options.onAbort) {
            this.options.onAbort(upload);
        }
    }

    checkAllComplete() {
        const allComplete = Array.from(this.uploads.values())
            .every(upload => upload.status === 'complete' || upload.status === 'error' || upload.status === 'aborted');

        if (allComplete && this.options.onComplete) {
            this.options.onComplete(Array.from(this.uploads.values()));
        }
    }

    // Public methods
    cancel(uploadId) {
        const upload = this.uploads.get(uploadId);
        if (!upload) return;

        if (upload.xhr) {
            upload.xhr.abort();
        }

        if (upload.multipartUploadId) {
            this.abortUpload(upload);
        }

        upload.status = 'aborted';
    }

    cancelAll() {
        this.uploads.forEach((upload, id) => {
            if (upload.status === 'uploading' || upload.status === 'pending') {
                this.cancel(id);
            }
        });
    }

    getUpload(uploadId) {
        return this.uploads.get(uploadId);
    }

    getUploads() {
        return Array.from(this.uploads.values());
    }

    clearCompleted() {
        this.uploads.forEach((upload, id) => {
            if (upload.status === 'complete' || upload.status === 'error' || upload.status === 'aborted') {
                this.uploads.delete(id);
            }
        });
    }

    destroy() {
        this.cancelAll();
        this.uploads.clear();
        if (this.fileInput && this.fileInput.parentNode) {
            this.fileInput.parentNode.removeChild(this.fileInput);
        }
    }

    // Utility methods
    generateId() {
        return 'upload-' + Date.now() + '-' + Math.random().toString(36).substring(2, 11);
    }

    getCsrfToken() {
        // First check if token was passed in options
        if (this.options.csrfToken) {
            return this.options.csrfToken;
        }

        // Check meta tag (standard Laravel approach)
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            return meta.getAttribute('content');
        }

        // Check for Laravel's window object
        if (typeof window !== 'undefined' && window.Laravel && window.Laravel.csrfToken) {
            return window.Laravel.csrfToken;
        }

        // Check for jQuery's CSRF setup
        if (typeof jQuery !== 'undefined' && jQuery.ajaxSettings && jQuery.ajaxSettings.headers) {
            return jQuery.ajaxSettings.headers['X-CSRF-TOKEN'] || '';
        }

        console.warn('ClientSideUploader: CSRF token not found. Make sure <meta name="csrf-token"> is present in your HTML.');
        return '';
    }

    formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// jQuery plugin wrapper
if (typeof jQuery !== 'undefined') {
    (function($) {
        $.fn.clientSideUploader = function(options) {
            return this.each(function() {
                const $this = $(this);
                if (!$this.data('clientSideUploader')) {
                    $this.data('clientSideUploader', new ClientSideUploader(this, options));
                }
            });
        };
    })(jQuery);
}

// Auto-initialize elements with data-client-uploader attribute
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('[data-client-uploader]');
    elements.forEach(element => {
        const options = element.dataset.clientUploaderOptions ?
            JSON.parse(element.dataset.clientUploaderOptions) : {};
        new ClientSideUploader(element, options);
    });
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ClientSideUploader;
}

// Global assignment
if (typeof window !== 'undefined') {
    window.ClientSideUploader = ClientSideUploader;
}
