@php
    $variant = $attributes->get('variant', 'primary');
    $size = $attributes->get('size', 'md');
    $iconPosition = $attributes->get('icon-position', 'left');
    $attributes = $attributes->except(['variant', 'size', 'icon-position']);

    // Base button classes
    $baseClasses = 'inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border focus:outline-hidden focus:ring-2 transition-colors duration-200 disabled:opacity-50 disabled:pointer-events-none';

    // Size variants
    $sizeClasses = [
        'xs' => 'py-1 px-2 text-xs',
        'sm' => 'py-1.5 px-3 text-sm',
        'md' => 'py-2 px-4',
        'lg' => 'py-3 px-5 text-base',
        'xl' => 'py-3.5 px-6 text-lg'
    ];

    // Button variants
    $variantClasses = [
        'primary' => 'border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700',
        'secondary' => 'border-gray-300 bg-white text-gray-800 hover:bg-gray-50 focus:ring-gray-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700',
        'outline' => 'border-gray-300 bg-transparent text-gray-800 hover:bg-gray-50 focus:ring-gray-500 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800',
        'ghost' => 'border-transparent bg-transparent text-gray-800 hover:bg-gray-100 focus:ring-gray-500 dark:text-neutral-200 dark:hover:bg-neutral-800',
        'danger' => 'border-transparent bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700',
        'success' => 'border-transparent bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700'
    ];

    $classes = $baseClasses . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);

    // Add any additional classes from attributes
    if ($attributes->has('class')) {
        $classes .= ' ' . $attributes->get('class');
    }
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon && $iconPosition === 'left')
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <use xlink:href="#{{$icon}}"></use>
        </svg>
    @endif

    {{ $label ?? $slot }}

    @if($icon && $iconPosition === 'right')
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <use xlink:href="#{{$icon}}"></use>
        </svg>
    @endif
</button>
