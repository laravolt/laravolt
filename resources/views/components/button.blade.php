@php
    $variant = $attributes->get('variant', 'primary');
    $size = $attributes->get('size', 'md');
    $iconPosition = $attributes->get('icon-position', 'left');
    $loading = $attributes->get('loading', false);
    $disabled = $attributes->get('disabled', false);
    $pill = $attributes->get('pill', false);
    $attributes = $attributes->except(['variant', 'size', 'icon-position', 'loading', 'disabled', 'pill']);

    // Base button classes with Preline UI v3.0 patterns
    $baseClasses = 'hs-button inline-flex items-center gap-x-2 text-sm font-medium border focus:outline-none transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none';
    
    // Enhanced size variants following Preline UI v3.0
    $sizeClasses = [
        '2xs' => 'py-1 px-2 text-xs',
        'xs' => 'py-1.5 px-2.5 text-xs',
        'sm' => 'py-2 px-3 text-sm',
        'md' => 'py-2.5 px-4 text-sm',
        'lg' => 'py-3 px-4 text-base',
        'xl' => 'py-3.5 px-5 text-base'
    ];

    // Updated button variants for Preline UI v3.0 with better contrast and modern styling
    $variantClasses = [
        'primary' => 'border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
        'secondary' => 'border-gray-200 bg-white text-gray-800 hover:bg-gray-50 focus:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:focus:ring-neutral-600',
        'soft' => 'border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 focus:bg-blue-200 focus:ring-4 focus:ring-blue-100 dark:bg-blue-900 dark:text-blue-400 dark:hover:bg-blue-800 dark:focus:bg-blue-800',
        'outline' => 'border-blue-600 text-blue-600 hover:border-blue-500 hover:text-blue-500 focus:border-blue-500 focus:text-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-blue-500 dark:text-blue-500 dark:hover:text-blue-400 dark:hover:border-blue-400 dark:focus:ring-blue-800',
        'ghost' => 'border-transparent text-blue-600 hover:bg-blue-100 hover:text-blue-800 focus:bg-blue-100 focus:text-blue-800 focus:ring-4 focus:ring-blue-100 dark:text-blue-500 dark:hover:bg-blue-900 dark:hover:text-blue-400 dark:focus:bg-blue-900 dark:focus:text-blue-400',
        'link' => 'border-transparent text-blue-600 hover:text-blue-800 focus:text-blue-800 underline-offset-4 hover:underline focus:underline dark:text-blue-500 dark:hover:text-blue-400 dark:focus:text-blue-400',
        'danger' => 'border-transparent bg-red-600 text-white hover:bg-red-700 focus:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900',
        'success' => 'border-transparent bg-teal-600 text-white hover:bg-teal-700 focus:bg-teal-700 focus:ring-4 focus:ring-teal-300 dark:bg-teal-600 dark:hover:bg-teal-700 dark:focus:ring-teal-800',
        'warning' => 'border-transparent bg-yellow-500 text-white hover:bg-yellow-600 focus:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800'
    ];

    // Rounded styles
    $roundedClasses = $pill ? 'rounded-full' : 'rounded-lg';

    // Loading state
    $loadingClasses = $loading ? 'pointer-events-none' : '';

    $classes = $baseClasses . ' ' . 
               ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . 
               ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . 
               $roundedClasses . ' ' . 
               $loadingClasses;
@endphp

<button {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled || $loading]) }}>
    @if($loading)
        <!-- Loading spinner -->
        <div class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
    @elseif($icon && $iconPosition === 'left')
        <!-- Left icon -->
        @if(str_contains($icon, '<svg'))
            {!! $icon !!}
        @else
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <use xlink:href="#{{$icon}}"></use>
            </svg>
        @endif
    @endif

    @if(!$loading || ($loading && ($label || $slot)))
        {{ $label ?? $slot }}
    @endif

    @if(!$loading && $icon && $iconPosition === 'right')
        <!-- Right icon -->
        @if(str_contains($icon, '<svg'))
            {!! $icon !!}
        @else
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <use xlink:href="#{{$icon}}"></use>
            </svg>
        @endif
    @endif
</button>
