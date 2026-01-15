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

    // Enhanced base input classes for Preline UI v3.0
    $baseClasses = 'py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600';

    // Enhanced variant styles for Preline UI v3.0
    $variantClasses = [
        'default' => $baseClasses,
        'error' => str_replace(['focus:border-blue-500', 'focus:ring-blue-500'], ['focus:border-red-500', 'focus:ring-red-500'], $baseClasses) . ' border-red-500 dark:border-red-600',
        'success' => str_replace(['focus:border-blue-500', 'focus:ring-blue-500'], ['focus:border-teal-500', 'focus:ring-teal-500'], $baseClasses) . ' border-teal-500 dark:border-teal-600'
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
