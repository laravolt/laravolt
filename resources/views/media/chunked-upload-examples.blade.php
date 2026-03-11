{{-- Chunked Upload Examples for Laravolt --}}

{{-- Resumable.js Example --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 dark:bg-neutral-800 dark:border-neutral-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Resumable.js Chunked Upload</h3>

    <div id="resumable-drop-zone" class="file-drop-zone flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-neutral-600 p-8 text-center" style="min-height: 200px;">
        <div class="text-center">
            <svg class="mx-auto size-10 text-gray-400 dark:text-neutral-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Drop files here or
                <button id="resumable-browse" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800 dark:text-blue-500 dark:hover:text-blue-400">Browse Files</button>
            </p>
        </div>
        <div class="border-t border-gray-200 dark:border-neutral-700 w-full my-4"></div>
        <div id="resumable-file-list" class="w-full"></div>
    </div>

    <div class="flex items-center gap-x-2 mt-4">
        <button id="resumable-upload" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:bg-green-700">Start Upload</button>
        <button id="resumable-pause" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-amber-500 text-white hover:bg-amber-600 focus:outline-none focus:bg-amber-600">Pause</button>
        <button id="resumable-cancel" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700">Cancel</button>
    </div>
</div>

{{-- FilePond Example --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 mt-6 dark:bg-neutral-800 dark:border-neutral-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">FilePond Chunked Upload</h3>

    <input type="file"
           class="filepond"
           name="files[]"
           multiple
           data-chunked-uploader='{"maxFiles": 5, "maxFileSize": 104857600}'>
</div>

{{-- Simple Auto-Init Example --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 mt-6 dark:bg-neutral-800 dark:border-neutral-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Auto-Initialize Example</h3>

    <div data-chunked-uploader
         data-chunked-uploader-options='{"chunkSize": 1048576, "maxFiles": 3}'
         class="file-drop-zone flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-neutral-600 p-8 text-center"
         style="min-height: 150px;">
        <div class="text-center">
            <svg class="mx-auto size-10 text-gray-400 dark:text-neutral-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Drop files here or
                <span class="file-browse-button text-blue-600 hover:text-blue-800 dark:text-blue-500 cursor-pointer font-semibold">Browse</span>
            </p>
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
            fileItem.className = 'flex items-center justify-between gap-4 p-3 bg-gray-50 rounded-lg dark:bg-neutral-700 mt-2';
            fileItem.innerHTML = `
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 truncate">${file.fileName}</p>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
                <div class="w-32" data-file-id="${file.uniqueIdentifier}">
                    <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700">
                        <div class="bar flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition-all duration-500" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="progress mt-1 text-xs text-gray-500 dark:text-neutral-400 text-end">0%</p>
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
                    progressBar.querySelector('.bar').classList.remove('bg-blue-600');
                    progressBar.querySelector('.bar').classList.add('bg-green-500');
                }
            }
        },
        onFileSuccess: function(file, response) {
            console.log('File uploaded successfully:', file.fileName, response);

            const fileItem = document.querySelector(`[data-file-id="${file.uniqueIdentifier}"]`).closest('.flex');
            fileItem.classList.add('border', 'border-green-200', 'bg-green-50', 'dark:bg-green-900/30', 'dark:border-green-800');

            if (response.files && response.files[0]) {
                const media = response.files[0];
                console.log('Media URL:', media.data.url);
                console.log('Media ID:', media.data.id);
            }
        },
        onFileError: function(file, message) {
            console.error('File upload failed:', file.fileName, message);

            const fileItem = document.querySelector(`[data-file-id="${file.uniqueIdentifier}"]`).closest('.flex');
            fileItem.classList.add('border', 'border-red-200', 'bg-red-50', 'dark:bg-red-900/30', 'dark:border-red-800');
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
    border-color: #10b981 !important;
    background-color: #ecfdf5 !important;
}

.file-drop-zone.uploading {
    border-color: #f59e0b !important;
    background-color: #fffbeb !important;
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
    border-radius: 0.5rem;
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
}
</style>
@endpush