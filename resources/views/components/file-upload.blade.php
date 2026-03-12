@php
    $id = $attributes->get('id', 'file-upload-' . uniqid());
    $name = $attributes->get('name', 'files');
    $multiple = $attributes->get('multiple', false);
    $accept = $attributes->get('accept', null);
    $maxSize = $attributes->get('max-size', null);
    $maxFiles = $attributes->get('max-files', null);
    $preview = $attributes->get('preview', true);
    $dragDrop = $attributes->get('drag-drop', true);
    $disabled = $attributes->get('disabled', false);
    $maxSizeFormatted = $maxSize ? (($maxSize >= 1024) ? round($maxSize / 1024, 1) . ' GB' : $maxSize . ' MB') : null;
    $acceptLabel = $accept ? str_replace(',', ', ', $accept) : 'All file types';
@endphp

<div id="{{ $id }}" {{ $attributes->except(['multiple', 'accept', 'max-size', 'max-files', 'preview', 'drag-drop', 'disabled']) }}>
    @if($dragDrop)
    <label for="{{ $id }}-input" class="group p-4 sm:p-7 block cursor-pointer text-center border-2 border-dashed border-gray-200 rounded-lg focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 dark:border-neutral-700 {{ $disabled ? 'opacity-50 pointer-events-none' : '' }}">
        <input id="{{ $id }}-input" name="{{ $name }}{{ $multiple ? '[]' : '' }}" type="file"
               class="sr-only" {{ $multiple ? 'multiple' : '' }}
               {{ $accept ? 'accept=' . $accept : '' }}
               {{ $disabled ? 'disabled' : '' }}
               onchange="handleFileUpload_{{ str_replace('-', '_', $id) }}(this)">
        <svg class="size-10 mx-auto text-gray-400 dark:text-neutral-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
        <span class="mt-2 block text-sm text-gray-800 dark:text-neutral-200">
            Browse your files or <span class="group-hover:text-blue-600 text-blue-500">drag and drop</span>
        </span>
        <span class="mt-1 block text-xs text-gray-500 dark:text-neutral-500">
            {{ $acceptLabel }}
            @if($maxSizeFormatted) · Max {{ $maxSizeFormatted }} @endif
            @if($maxFiles) · Max {{ $maxFiles }} files @endif
        </span>
    </label>
    @else
    <input id="{{ $id }}-input" name="{{ $name }}{{ $multiple ? '[]' : '' }}" type="file"
           class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none dark:text-neutral-500 dark:file:bg-blue-500 dark:hover:file:bg-blue-400"
           {{ $multiple ? 'multiple' : '' }}
           {{ $accept ? 'accept=' . $accept : '' }}
           {{ $disabled ? 'disabled' : '' }}
           onchange="handleFileUpload_{{ str_replace('-', '_', $id) }}(this)">
    @endif

    @if($preview)
    <div id="{{ $id }}-preview" class="mt-4 space-y-2"></div>
    @endif
</div>

@if($preview)
@push('script')
<script>
function handleFileUpload_{{ str_replace('-', '_', $id) }}(input) {
    var preview = document.getElementById('{{ $id }}-preview');
    if (!preview) return;
    preview.innerHTML = '';

    Array.from(input.files).forEach(function(file) {
        var item = document.createElement('div');
        item.className = 'p-3 bg-white border border-solid border-gray-300 rounded-xl dark:bg-neutral-800 dark:border-neutral-600';

        var inner = document.createElement('div');
        inner.className = 'flex justify-between items-center';

        var left = document.createElement('div');
        left.className = 'flex items-center gap-x-3';

        var iconWrap = document.createElement('span');
        iconWrap.className = 'size-10 flex justify-center items-center border border-gray-200 text-gray-500 rounded-lg dark:border-neutral-700 dark:text-neutral-500';
        iconWrap.innerHTML = '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>';

        var info = document.createElement('div');
        var name = document.createElement('p');
        name.className = 'text-sm font-medium text-gray-800 dark:text-white';
        name.textContent = file.name;
        var size = document.createElement('p');
        size.className = 'text-xs text-gray-500 dark:text-neutral-500';
        size.textContent = (file.size / 1024).toFixed(1) + ' KB';
        info.appendChild(name);
        info.appendChild(size);

        left.appendChild(iconWrap);
        left.appendChild(info);
        inner.appendChild(left);
        item.appendChild(inner);
        preview.appendChild(item);
    });
}
</script>
@endpush
@endif
