<div class="direct-upload-component">
    <div class="upload-zone" wire:loading.remove wire:target="file">
        <label for="file-input-{{ $this->getId() }}" class="file-upload-label">
            <div class="upload-prompt">
                <svg xmlns="http://www.w3.org/2000/svg" class="upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="upload-text">Click to upload or drag and drop</p>
                <p class="upload-hint">Maximum file size: {{ number_format($maxFileSize / 1024, 0) }}MB</p>
            </div>
            <input 
                type="file" 
                id="file-input-{{ $this->getId() }}" 
                wire:model="file"
                class="file-input-hidden"
                accept="*/*"
            />
        </label>
    </div>

    <div wire:loading wire:target="file" class="upload-loading">
        <div class="loading-spinner">
            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <p class="loading-text">Uploading file...</p>
    </div>

    @error('file')
        <div class="error-message">
            <svg xmlns="http://www.w3.org/2000/svg" class="error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    @if(count($uploadedFiles) > 0)
        <div class="uploaded-files-list">
            <h3 class="uploaded-files-title">Uploaded Files</h3>
            @foreach($uploadedFiles as $uploadedFile)
                <div class="uploaded-file-item">
                    <div class="file-info">
                        @if(str_starts_with($uploadedFile['type'], 'image/'))
                            <img src="{{ $uploadedFile['thumbnail'] }}" alt="{{ $uploadedFile['name'] }}" class="file-thumbnail">
                        @else
                            <div class="file-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="file-details">
                            <p class="file-name">{{ $uploadedFile['name'] }}</p>
                            <p class="file-size">{{ number_format($uploadedFile['size'] / 1024, 2) }} KB</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="removeFile({{ $uploadedFile['id'] }})"
                        class="remove-button"
                        wire:loading.attr="disabled"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .direct-upload-component {
        width: 100%;
        max-width: 600px;
    }

    .upload-zone {
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .upload-zone:hover {
        border-color: #4299e1;
        background-color: #f7fafc;
    }

    .file-upload-label {
        cursor: pointer;
        display: block;
    }

    .upload-prompt {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .upload-icon {
        width: 48px;
        height: 48px;
        color: #4299e1;
    }

    .upload-text {
        font-size: 1rem;
        font-weight: 500;
        color: #2d3748;
        margin: 0;
    }

    .upload-hint {
        font-size: 0.875rem;
        color: #718096;
        margin: 0;
    }

    .file-input-hidden {
        display: none;
    }

    .upload-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 2rem;
    }

    .loading-spinner {
        width: 48px;
        height: 48px;
    }

    .loading-spinner svg {
        width: 100%;
        height: 100%;
        color: #4299e1;
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .loading-text {
        font-size: 1rem;
        color: #4a5568;
        margin: 0;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
        background-color: #fed7d7;
        border: 1px solid #fc8181;
        border-radius: 6px;
        color: #742a2a;
    }

    .error-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .uploaded-files-list {
        margin-top: 1.5rem;
    }

    .uploaded-files-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 1rem 0;
    }

    .uploaded-file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        margin-bottom: 0.5rem;
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .file-thumbnail {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 4px;
    }

    .file-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e2e8f0;
        border-radius: 4px;
    }

    .file-icon svg {
        width: 28px;
        height: 28px;
        color: #4a5568;
    }

    .file-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .file-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: #2d3748;
        margin: 0;
    }

    .file-size {
        font-size: 0.75rem;
        color: #718096;
        margin: 0;
    }

    .remove-button {
        padding: 0.5rem;
        background: none;
        border: none;
        cursor: pointer;
        color: #e53e3e;
        transition: color 0.2s ease;
    }

    .remove-button:hover {
        color: #c53030;
    }

    .remove-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .remove-button svg {
        width: 20px;
        height: 20px;
    }
</style>
