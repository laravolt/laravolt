/**
 * Laravolt Chunked Upload with FilePond
 * 
 * Example implementation for chunked file upload using FilePond
 * Compatible with Laravolt's ChunkedMediaHandler
 */

// Import FilePond and plugins
import { create, registerPlugin } from 'filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginChunkUpload from 'filepond-plugin-chunk-upload';

// Register plugins
registerPlugin(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginChunkUpload
);

class LaravoltFilePondUpload {
    constructor(element, options = {}) {
        this.options = {
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            chunkUploads: true,
            chunkForce: true,
            chunkRetryDelays: [0, 1000, 3000, 5000],
            maxFileSize: '100MB',
            acceptedFileTypes: ['image/*', 'video/*', 'application/pdf'],
            ...options
        };

        this.pond = this.createPond(element);
    }

    createPond(element) {
        return create(element, {
            // Chunk upload configuration
            chunkUploads: this.options.chunkUploads,
            chunkForce: this.options.chunkForce,
            chunkSize: this.options.chunkSize,
            chunkRetryDelays: this.options.chunkRetryDelays,

            // Server configuration
            server: {
                url: '/media',
                process: {
                    url: '/chunk',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    onload: (response) => {
                        // Handle chunk upload response
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
                },
                revert: {
                    url: '/media/chunk',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                },
                restore: {
                    url: '/media/chunk/restore',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                },
                load: {
                    url: '/media/',
                    method: 'GET'
                }
            },

            // File validation
            maxFileSize: this.options.maxFileSize,
            acceptedFileTypes: this.options.acceptedFileTypes,

            // Event handlers
            onaddfile: (error, file) => {
                if (error) {
                    console.error('Error adding file:', error);
                    return;
                }
                console.log('File added:', file.filename);
                if (this.options.onFileAdded) {
                    this.options.onFileAdded(file);
                }
            },

            onprocessfile: (error, file) => {
                if (error) {
                    console.error('Error processing file:', error);
                    if (this.options.onError) {
                        this.options.onError(file, error);
                    }
                    return;
                }
                console.log('File processed successfully:', file.filename);
                if (this.options.onSuccess) {
                    this.options.onSuccess(file);
                }
            },

            onprocessfiles: () => {
                console.log('All files processed');
                if (this.options.onComplete) {
                    this.options.onComplete();
                }
            },

            onupdatefiles: (files) => {
                console.log('Files updated:', files.length);
                if (this.options.onFilesUpdate) {
                    this.options.onFilesUpdate(files);
                }
            }
        });
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    /**
     * Destroy FilePond instance
     */
    destroy() {
        this.pond.destroy();
    }

    /**
     * Get files
     */
    getFiles() {
        return this.pond.getFiles();
    }

    /**
     * Add files
     */
    addFiles(files) {
        this.pond.addFiles(files);
    }

    /**
     * Remove all files
     */
    removeFiles() {
        this.pond.removeFiles();
    }
}

// Example usage
document.addEventListener('DOMContentLoaded', function() {
    // Create FilePond instance
    const filePondUpload = new LaravoltFilePondUpload('#filepond-upload', {
        chunkSize: 2 * 1024 * 1024, // 2MB chunks
        maxFileSize: '100MB',
        acceptedFileTypes: ['image/*', 'video/*', 'application/pdf'],
        
        onFileAdded: function(file) {
            console.log('File added:', file.filename);
            // Update UI
        },
        
        onSuccess: function(file) {
            console.log('File uploaded successfully:', file.filename);
            // Handle successful upload
            const serverId = file.serverId;
            console.log('Server ID:', serverId);
        },
        
        onError: function(file, error) {
            console.error('Upload error:', error);
            // Show error message
        },
        
        onComplete: function() {
            console.log('All uploads completed');
            // Reset UI or show completion message
        },
        
        onFilesUpdate: function(files) {
            console.log('Files updated:', files.length);
            // Update file list UI
        }
    });

    // Example: Get uploaded files
    const getUploadedFiles = () => {
        const files = filePondUpload.getFiles();
        const uploadedFiles = files.filter(file => file.status === 5); // FilePond.FileStatus.PROCESSING_COMPLETE
        console.log('Uploaded files:', uploadedFiles);
        return uploadedFiles;
    };

    // Example: Handle form submission
    const form = document.getElementById('upload-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const uploadedFiles = getUploadedFiles();
            console.log('Submitting form with files:', uploadedFiles);
            // Submit form with file IDs
        });
    }
});

// Export for module usage
export default LaravoltFilePondUpload;