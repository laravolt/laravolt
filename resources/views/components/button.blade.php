@php
    $base = 'inline-flex items-center justify-center gap-x-2 rounded-lg text-sm font-medium focus:outline-hidden transition-all disabled:opacity-50 disabled:pointer-events-none';
    $variant = $attributes->get('variant', 'primary');
    $size = $attributes->get('size', 'md');

    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600',
        'secondary' => 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 focus:bg-gray-100 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700',
        'soft' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 focus:bg-blue-100 border border-transparent dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600',
        'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700',
    ][$variant] ?? '';

    $sizeClasses = [
        'sm' => 'px-3 py-2',
        'md' => 'px-3.5 py-2.5',
        'lg' => 'px-4 py-3',
    ][$size] ?? '';
@endphp

<button {{ $attributes->class([$base, $variantClasses, $sizeClasses]) }}>
    @isset($icon)
        <i class="{{ $icon }}"></i>
    @endisset
    {{ $label ?? $slot }}
</button>
