@php
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'default');
    $label = $attributes->get('label', null);
    $helper = $attributes->get('helper', null);
    $error = $attributes->get('error', null);
    $success = $attributes->get('success', null);
    $rows = $attributes->get('rows', 3);
    $attributes = $attributes->except(['size', 'variant', 'label', 'helper', 'error', 'success', 'rows']);

    $textareaId = $attributes->get('id', 'textarea-' . uniqid());

    // Size variants
    $sizeClasses = [
        'sm' => 'py-2 px-3 text-sm',
        'md' => 'py-3 px-4 text-sm',
        'lg' => 'py-3.5 px-4 text-base'
    ];

    // Base textarea classes
    $baseClasses = 'block w-full border rounded-lg shadow-sm focus:outline-hidden focus:ring-2 transition-colors duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:text-neutral-200 dark:placeholder-neutral-500 dark:border-neutral-700 resize-vertical';

    // Variant styles
    $variantClasses = [
        'default' => $baseClasses . ' border-gray-300 bg-white text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800',
        'error' => $baseClasses . ' border-red-300 bg-white text-gray-900 placeholder-gray-500 focus:border-red-500 focus:ring-red-500 dark:border-red-800 dark:bg-neutral-800',
        'success' => $baseClasses . ' border-green-300 bg-white text-gray-900 placeholder-gray-500 focus:border-green-500 focus:ring-green-500 dark:border-green-800 dark:bg-neutral-800'
    ];

    $classes = ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($variantClasses[$variant] ?? $variantClasses['default']);
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $textareaId }}" class="block text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >{{ $slot }}</textarea>

    @if($helper && !$error)
        <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $helper }}</p>
    @endif

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($success)
        <p class="text-sm text-green-600 dark:text-green-400">{{ $success }}</p>
    @endif
</div>
