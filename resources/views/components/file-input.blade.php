@php
    $label = $attributes->get('label', null);
    $helper = $attributes->get('helper', null);
    $error = $attributes->get('error', null);
    $multiple = $attributes->get('multiple', false);
    $accept = $attributes->get('accept', null);
    $maxSize = $attributes->get('max-size', null);
    $dragDrop = $attributes->get('drag-drop', true);
    $attributes = $attributes->except(['label', 'helper', 'error', 'multiple', 'accept', 'max-size', 'drag-drop']);

    $inputId = $attributes->get('id', 'file-input-' . uniqid());
    $name = $attributes->get('name', $inputId);
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    @if($dragDrop)
        <!-- Drag and Drop Area -->
        <div
            class="relative border-2 border-dashed rounded-lg p-6 text-center hover:bg-gray-50 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 transition-colors duration-200 dark:hover:bg-neutral-800 dark:border-neutral-700"
            x-data="{
                isDragOver: false,
                files: [],
                handleDragOver(event) {
                    event.preventDefault();
                    this.isDragOver = true;
                },
                handleDragLeave(event) {
                    event.preventDefault();
                    this.isDragOver = false;
                },
                handleDrop(event) {
                    event.preventDefault();
                    this.isDragOver = false;
                    const files = Array.from(event.dataTransfer.files);
                    this.files = files;
                    // Update the input
                    const input = document.getElementById('{{ $inputId }}');
                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));
                    input.files = dt.files;
                }
            }"
            x-bind:class="{ 'border-blue-400 bg-blue-50 dark:bg-blue-900/20': isDragOver }"
            @dragover="handleDragOver($event)"
            @dragleave="handleDragLeave($event)"
            @drop="handleDrop($event)"
        >
            <input
                id="{{ $inputId }}"
                name="{{ $multiple ? $name . '[]' : $name }}"
                type="file"
                {{ $multiple ? 'multiple' : '' }}
                {{ $accept ? 'accept="' . $accept . '"' : '' }}
                {{ $attributes->merge(['class' => 'absolute inset-0 w-full h-full opacity-0 cursor-pointer']) }}
            />

            <div class="space-y-4">
                <!-- Upload Icon -->
                <div class="mx-auto w-12 h-12 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>

                <!-- Upload Text -->
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        Drop files here or
                        <span class="text-blue-600 hover:text-blue-700 dark:text-blue-400 cursor-pointer">browse</span>
                    </p>
                    @if($maxSize)
                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                            Maximum file size: {{ $maxSize }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Simple File Input -->
        <div class="relative">
            <input
                id="{{ $inputId }}"
                name="{{ $multiple ? $name . '[]' : $name }}"
                type="file"
                {{ $multiple ? 'multiple' : '' }}
                {{ $accept ? 'accept="' . $accept . '"' : '' }}
                {{ $attributes->merge(['class' => 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-neutral-400 dark:file:bg-neutral-800 dark:file:text-neutral-200']) }}
            />
        </div>
    @endif

    @if($helper && !$error)
        <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $helper }}</p>
    @endif

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
