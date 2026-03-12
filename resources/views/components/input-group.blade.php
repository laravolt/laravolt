@php
    $prefix = $attributes->get('prefix', null);
    $suffix = $attributes->get('suffix', null);
    $prefixIcon = $attributes->get('prefix-icon', null);
    $suffixIcon = $attributes->get('suffix-icon', null);
    $size = $attributes->get('size', 'md');
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-sm',
        'lg' => 'text-base',
    ];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="flex rounded-lg shadow-sm" {{ $attributes->except(['prefix', 'suffix', 'prefix-icon', 'suffix-icon', 'size']) }}>
    @if($prefix)
        <span class="px-4 inline-flex items-center min-w-fit rounded-s-lg border border-e-0 border-gray-200 bg-gray-50 {{ $currentSize }} text-gray-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400">
            {{ $prefix }}
        </span>
    @endif

    {{ $slot }}

    @if($suffix)
        <span class="px-4 inline-flex items-center min-w-fit rounded-e-lg border border-s-0 border-gray-200 bg-gray-50 {{ $currentSize }} text-gray-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400">
            {{ $suffix }}
        </span>
    @endif
</div>
