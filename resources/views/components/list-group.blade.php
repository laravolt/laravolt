@php
    $variant = $attributes->get('variant', 'default');
    $flush = $attributes->get('flush', false);
    $attributes = $attributes->except(['variant', 'flush']);

    // Variant styles
    $variantClasses = [
        'default' => 'divide-y divide-gray-200 dark:divide-neutral-700',
        'flush' => 'divide-y divide-gray-200 dark:divide-neutral-700 border-0'
    ];

    $containerClasses = 'bg-white border border-gray-200 rounded-lg overflow-hidden dark:bg-neutral-900 dark:border-neutral-700 ' . ($variantClasses[$variant] ?? $variantClasses['default']);
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    {{ $slot }}
</div>
