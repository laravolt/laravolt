@php
    $variant = $attributes->get('variant', 'default');
    $size = $attributes->get('size', 'sm');
    $dot = $attributes->get('dot', false);
    $attributes = $attributes->except(['variant', 'size', 'dot']);

    // Size variants
    $sizeClasses = [
        'xs' => 'px-1.5 py-0.5 text-xs',
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-2.5 py-1.5 text-sm',
        'lg' => 'px-3 py-2 text-base'
    ];

    // Variant styles
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-200',
        'primary' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'secondary' => 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-200',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'info' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
        'light' => 'bg-white text-gray-800 border border-gray-300 dark:bg-neutral-800 dark:text-neutral-200 dark:border-neutral-700',
        'dark' => 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
    ];

    $classes = 'inline-flex items-center font-medium rounded-full transition-colors duration-200 ' . ($sizeClasses[$size] ?? $sizeClasses['sm']) . ' ' . ($variantClasses[$variant] ?? $variantClasses['default']);

    // Add dot indicator if specified
    if ($dot) {
        $classes .= ' gap-1.5';
    }
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-2 h-2 bg-current rounded-full flex-shrink-0"></span>
    @endif

    {{ $label ?? $slot }}
</span>
