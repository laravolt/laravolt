@php
    $id = $attributes->get('id', 'file-progress-' . uniqid());
    $fileName = $attributes->get('file-name', 'file.pdf');
    $progress = $attributes->get('progress', 0);
    $fileSize = $attributes->get('file-size', '0 KB');
    $status = $attributes->get('status', 'uploading');
    $statusConfig = [
        'uploading' => ['color' => 'bg-blue-600', 'text' => 'Uploading...', 'icon' => 'animate'],
        'complete' => ['color' => 'bg-teal-500', 'text' => 'Complete', 'icon' => 'check'],
        'error' => ['color' => 'bg-red-500', 'text' => 'Error', 'icon' => 'x'],
    ];
    $current = $statusConfig[$status] ?? $statusConfig['uploading'];
@endphp

<div id="{{ $id }}" class="p-3 bg-white border border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700" {{ $attributes->except(['file-name', 'progress', 'file-size', 'status']) }}>
    <div class="flex items-center gap-x-3">
        <span class="size-10 flex justify-center items-center border border-gray-200 text-gray-500 rounded-lg dark:border-neutral-700 dark:text-neutral-500">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        </span>
        <div class="grow">
            <div class="flex justify-between items-center gap-x-2">
                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $fileName }}</p>
                <p class="text-xs text-gray-500 dark:text-neutral-500 whitespace-nowrap">{{ $progress }}%</p>
            </div>
            <div class="flex items-center gap-x-2 mt-1">
                <div class="flex w-full h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700">
                    <div class="{{ $current['color'] }} rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-neutral-500">{{ $fileSize }} · {{ $current['text'] }}</p>
        </div>
    </div>
</div>
