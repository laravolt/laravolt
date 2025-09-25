{{-- Chunked Upload Examples for Laravolt --}}

{{-- Resumable.js Example --}}
<div class="ui segment">
    <h3 class="ui header">Resumable.js Chunked Upload</h3>
    
    <div id="resumable-drop-zone" class="ui placeholder segment file-drop-zone" style="min-height: 200px; border: 2px dashed #ddd;">
        <div class="ui icon header">
            <i class="upload icon"></i>
            Drop files here or <button id="resumable-browse" class="ui button primary file-browse-button">Browse Files</button>
        </div>
        <div class="ui divider"></div>
        <div id="resumable-file-list"></div>
    </div>
    
    <div class="ui buttons" style="margin-top: 10px;">
        <button id="resumable-upload" class="ui button green">Start Upload</button>
        <button id="resumable-pause" class="ui button orange">Pause</button>
        <button id="resumable-cancel" class="ui button red">Cancel</button>
    </div>
</div>

{{-- FilePond Example --}}
<div class="ui segment">
    <h3 class="ui header">FilePond Chunked Upload</h3>
    
    <input type="file" 
           class="filepond" 
           name="files[]" 
           multiple 
           data-chunked-uploader='{"maxFiles": 5, "maxFileSize": 104857600}'>
</div>

{{-- Simple Auto-Init Example --}}
<div class="ui segment">
    <h3 class="ui header">Auto-Initialize Example</h3>
    
    <div data-chunked-uploader 
         data-chunked-uploader-options='{"chunkSize": 1048576, "maxFiles": 3}'
         class="ui placeholder segment file-drop-zone" 
         style="min-height: 150px; border: 2px dashed #ddd;">
        <div class="ui icon header">
            <i class="upload icon"></i>
            Drop files here or <span class="file-browse-button ui button">Browse</span>
        </div>
    </div>
</div>

{{-- Required Scripts --}}
@push('scripts')
{{-- Include Resumable.js --}}
<script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>

{{-- Include FilePond (optional) --}}
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>

{{-- Include our chunked uploader --}}
<script src="{{ asset('js/components/chunked-uploader.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manual Resumable.js initialization example
    const resumableUploader = new ChunkedUploader(document.getElementById('resumable-drop-zone'), {
        maxFileSize: 100 * 1024 * 1024, // 100MB
        chunkSize: 2 * 1024 * 1024, // 2MB chunks
        maxFiles: 10,
        onFileAdded: function(file) {
            console.log('File added:', file.fileName);
            
            // Add file to the list
            const fileList = document.getElementById('resumable-file-list');
            const fileItem = document.createElement('div');
            fileItem.className = 'ui segment';
            fileItem.innerHTML = `
                <div class="ui grid">
                    <div class="twelve wide column">
                        <strong>${file.fileName}</strong><br>
                        <small>Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    </div>
                    <div class="four wide column">
                        <div class="ui progress" data-file-id="${file.uniqueIdentifier}">
                            <div class="bar">
                                <div class="progress">0%</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            fileList.appendChild(fileItem);
        },
        onFileProgress: function(file, progress) {
            const progressBar = document.querySelector(`[data-file-id="${file.uniqueIdentifier}"]`);
            if (progressBar) {
                const percentage = Math.round(progress * 100);
                progressBar.querySelector('.bar').style.width = percentage + '%';
                progressBar.querySelector('.progress').textContent = percentage + '%';
                
                if (percentage === 100) {
                    progressBar.classList.add('success');
                }
            }
        },
        onFileSuccess: function(file, response) {
            console.log('File uploaded successfully:', file.fileName, response);
            
            // Show success message
            const fileItem = document.querySelector(`[data-file-id="${file.uniqueIdentifier}"]`).closest('.segment');
            fileItem.classList.add('positive');
            
            // You can access the media data from response.files[0]
            if (response.files && response.files[0]) {
                const media = response.files[0];
                console.log('Media URL:', media.data.url);
                console.log('Media ID:', media.data.id);
            }
        },
        onFileError: function(file, message) {
            console.error('File upload failed:', file.fileName, message);
            
            // Show error message
            const fileItem = document.querySelector(`[data-file-id="${file.uniqueIdentifier}"]`).closest('.segment');
            fileItem.classList.add('negative');
        },
        onComplete: function() {
            console.log('All uploads completed');
        }
    });

    // Manual control buttons
    document.getElementById('resumable-upload').addEventListener('click', function() {
        resumableUploader.upload();
    });

    document.getElementById('resumable-pause').addEventListener('click', function() {
        resumableUploader.pause();
    });

    document.getElementById('resumable-cancel').addEventListener('click', function() {
        resumableUploader.cancel();
    });

    // FilePond initialization (if FilePond is available)
    if (typeof FilePond !== 'undefined') {
        // Register plugins
        FilePond.registerPlugin(FilePondPluginFileValidateSize);
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        
        // Initialize FilePond elements
        document.querySelectorAll('.filepond').forEach(element => {
            new ChunkedUploader(element, {
                onFileSuccess: function(file, response) {
                    console.log('FilePond upload success:', file, response);
                },
                onFileError: function(file, error) {
                    console.error('FilePond upload error:', file, error);
                }
            });
        });
    }
});
</script>
@endpush

{{-- Required Styles --}}
@push('styles')
{{-- FilePond styles --}}
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">

<style>
.file-drop-zone {
    transition: all 0.3s ease;
}

.file-drop-zone.dragover {
    border-color: #21ba45 !important;
    background-color: #f8fff8 !important;
}

.file-drop-zone.uploading {
    border-color: #fbbd08 !important;
    background-color: #fffbf0 !important;
}

.ui.progress .bar {
    transition: width 0.3s ease;
}

.ui.progress.success .bar {
    background-color: #21ba45 !important;
}

/* FilePond customizations */
.filepond--root {
    margin: 1em 0;
}

.filepond--drop-label {
    color: #4a4a4a;
}

.filepond--label-action {
    text-decoration-color: #babdc0;
}

.filepond--panel-root {
    border-radius: 0.28571429rem;
    background-color: #ffffff;
    border: 1px solid rgba(34, 36, 38, 0.15);
}
</style>
@endpush