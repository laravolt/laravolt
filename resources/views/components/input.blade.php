@php
    $type = $attributes->get('type', 'text');
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'default');
    $label = $attributes->get('label', null);
    $helper = $attributes->get('helper', null);
    $error = $attributes->get('error', null);
    $success = $attributes->get('success', null);
    $icon = $attributes->get('icon', null);
    $iconPosition = $attributes->get('icon-position', 'left');
    $attributes = $attributes->except(['type', 'size', 'variant', 'label', 'helper', 'error', 'success', 'icon', 'icon-position']);

    $inputId = $attributes->get('id', 'input-' . uniqid());

    // Size variants
    $sizeClasses = [
        'sm' => 'py-2 px-3 text-sm',
        'md' => 'py-3 px-4 text-sm',
        'lg' => 'py-3.5 px-4 text-base'
    ];

    // Base input classes
    $baseClasses = 'block w-full border rounded-lg shadow-sm focus:outline-hidden focus:ring-2 transition-colors duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:text-neutral-200 dark:placeholder-neutral-500';

    // Variant styles
    $variantClasses = [
        'default' => $baseClasses . ' border-gray-300 bg-white text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800',
        'error' => $baseClasses . ' border-red-300 bg-white text-gray-900 placeholder-gray-500 focus:border-red-500 focus:ring-red-500 dark:border-red-800 dark:bg-neutral-800',
        'success' => $baseClasses . ' border-green-300 bg-white text-gray-900 placeholder-gray-500 focus:border-green-500 focus:ring-green-500 dark:border-green-800 dark:bg-neutral-800'
    ];

    $classes = ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($variantClasses[$variant] ?? $variantClasses['default']);

    // Add icon padding if icon is present
    if ($icon) {
        $classes .= $iconPosition === 'left' ? ' pl-10' : ' pr-10';
    }
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            {{ $attributes->merge(['class' => $classes]) }}
        />

        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        @endif
    </div>

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
