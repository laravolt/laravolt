/**
 * Chunked Uploader for Laravolt
 * Supports both Resumable.js and FilePond implementations
 */

class ChunkedUploader {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            // Default options
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            maxFileSize: 100 * 1024 * 1024, // 100MB max
            simultaneousUploads: 1,
            testChunks: true,
            throttleProgressCallbacks: 1,
            // Laravolt specific endpoints
            target: '/media/chunk',
            testTarget: '/media/chunk/status',
            permanentErrors: [400, 404, 415, 500, 501],
            // Callbacks
            onFileAdded: null,
            onFileSuccess: null,
            onFileError: null,
            onFileProgress: null,
            onComplete: null,
            onError: null,
            // File validation
            fileType: null,
            maxFiles: null,
            ...options
        };

        this.uploader = null;
        this.init();
    }

    init() {
        if (typeof Resumable !== 'undefined') {
            this.initResumable();
        } else if (typeof FilePond !== 'undefined') {
            this.initFilePond();
        } else {
            console.error('ChunkedUploader: Neither Resumable.js nor FilePond is available');
        }
    }

    initResumable() {
        this.uploader = new Resumable({
            target: this.options.target,
            testTarget: this.options.testTarget,
            chunkSize: this.options.chunkSize,
            maxFileSize: this.options.maxFileSize,
            simultaneousUploads: this.options.simultaneousUploads,
            testChunks: this.options.testChunks,
            throttleProgressCallbacks: this.options.throttleProgressCallbacks,
            permanentErrors: this.options.permanentErrors,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            query: {
                handler: 'chunked'
            }
        });

        if (!this.uploader.support) {
            console.error('ChunkedUploader: Resumable.js is not supported in this browser');
            return;
        }

        // Assign browse and drop targets
        if (this.element.classList.contains('file-drop-zone')) {
            this.uploader.assignDrop(this.element);
        }
        
        const browseButton = this.element.querySelector('.file-browse-button') || this.element;
        this.uploader.assignBrowse(browseButton);

        // Bind events
        this.bindResumableEvents();
    }

    initFilePond() {
        // FilePond configuration for chunked uploads
        const pondOptions = {
            server: {
                url: '/media/',
                process: {
                    url: 'chunk',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    ondata: (formData) => {
                        formData.append('handler', 'chunked');
                        return formData;
                    }
                },
                revert: null,
                restore: null,
                load: null,
                fetch: null
            },
            chunkUploads: true,
            chunkSize: this.options.chunkSize,
            chunkRetryDelays: [500, 1000, 3000],
            maxFileSize: this.options.maxFileSize + 'B',
            maxFiles: this.options.maxFiles,
            acceptedFileTypes: this.options.fileType ? [this.options.fileType] : null,
            labelIdle: 'Drag & Drop your files or <span class="filepond--label-action">Browse</span>',
            onprocessfile: (error, file) => {
                if (error) {
                    this.options.onFileError && this.options.onFileError(file, error);
                } else {
                    this.options.onFileSuccess && this.options.onFileSuccess(file);
                }
            },
            onprocessfileprogress: (file, progress) => {
                this.options.onFileProgress && this.options.onFileProgress(file, progress);
            },
            onaddfile: (error, file) => {
                if (!error) {
                    this.options.onFileAdded && this.options.onFileAdded(file);
                }
            }
        };

        this.uploader = FilePond.create(this.element, pondOptions);
    }

    bindResumableEvents() {
        // File added event
        this.uploader.on('fileAdded', (file) => {
            if (this.options.onFileAdded) {
                this.options.onFileAdded(file);
            }
            
            // Auto start upload
            this.uploader.upload();
        });

        // File success event
        this.uploader.on('fileSuccess', (file, message) => {
            try {
                const response = JSON.parse(message);
                if (this.options.onFileSuccess) {
                    this.options.onFileSuccess(file, response);
                }
            } catch (e) {
                console.error('ChunkedUploader: Invalid JSON response', message);
            }
        });

        // File error event
        this.uploader.on('fileError', (file, message) => {
            if (this.options.onFileError) {
                this.options.onFileError(file, message);
            }
        });

        // File progress event
        this.uploader.on('fileProgress', (file) => {
            if (this.options.onFileProgress) {
                this.options.onFileProgress(file, file.progress());
            }
        });

        // Complete event (all files uploaded)
        this.uploader.on('complete', () => {
            if (this.options.onComplete) {
                this.options.onComplete();
            }
        });

        // Upload start event
        this.uploader.on('uploadStart', () => {
            this.element.classList.add('uploading');
        });

        // Pause event
        this.uploader.on('pause', () => {
            this.element.classList.remove('uploading');
        });
    }

    // Public methods
    upload() {
        if (this.uploader && this.uploader.upload) {
            this.uploader.upload();
        }
    }

    pause() {
        if (this.uploader && this.uploader.pause) {
            this.uploader.pause();
        }
    }

    cancel() {
        if (this.uploader && this.uploader.cancel) {
            this.uploader.cancel();
        }
    }

    addFile(file) {
        if (this.uploader && this.uploader.addFile) {
            this.uploader.addFile(file);
        }
    }

    removeFile(file) {
        if (this.uploader && this.uploader.removeFile) {
            this.uploader.removeFile(file);
        }
    }

    getFiles() {
        if (this.uploader && this.uploader.files) {
            return this.uploader.files;
        }
        return [];
    }

    destroy() {
        if (this.uploader) {
            if (typeof this.uploader.destroy === 'function') {
                this.uploader.destroy();
            }
            this.uploader = null;
        }
    }
}

// jQuery plugin wrapper
if (typeof jQuery !== 'undefined') {
    (function($) {
        $.fn.chunkedUploader = function(options) {
            return this.each(function() {
                const $this = $(this);
                if (!$this.data('chunkedUploader')) {
                    $this.data('chunkedUploader', new ChunkedUploader(this, options));
                }
            });
        };
    })(jQuery);
}

// Auto-initialize elements with data-chunked-uploader attribute
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('[data-chunked-uploader]');
    elements.forEach(element => {
        const options = element.dataset.chunkedUploaderOptions ? 
            JSON.parse(element.dataset.chunkedUploaderOptions) : {};
        new ChunkedUploader(element, options);
    });
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChunkedUploader;
}

// Global assignment
if (typeof window !== 'undefined') {
    window.ChunkedUploader = ChunkedUploader;
}