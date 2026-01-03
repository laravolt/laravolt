@php
    $variant = $attributes->get('variant', 'default');
    $iconPosition = $attributes->get('icon-position', 'left');
    $attributes = $attributes->except(['variant', 'icon-position']);

    $linkClasses = match($variant) {
        'primary' => 'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300',
        'secondary' => 'text-gray-600 hover:text-gray-800 dark:text-neutral-400 dark:hover:text-neutral-200',
        'muted' => 'text-gray-500 hover:text-gray-700 dark:text-neutral-500 dark:hover:text-neutral-300',
        default => 'text-gray-900 hover:text-gray-700 dark:text-white dark:hover:text-neutral-300'
    };

    $baseClasses = 'inline-flex items-center gap-x-2 text-sm font-medium transition-colors duration-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500 rounded';
@endphp

<a href="{{ $url }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $linkClasses]) }}>
    @if($icon && $iconPosition === 'left')
        <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <use xlink:href="#{{$icon}}"></use>
        </svg>
    @endif

    {{ $label ?? $slot }}

    @if($icon && $iconPosition === 'right')
        <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <use xlink:href="#{{$icon}}"></use>
        </svg>
    @endif
</a>
