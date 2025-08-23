@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-colors';

$variants = [
    'primary' => 'border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    'secondary' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-slate-800',
    'success' => 'border-transparent bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'danger' => 'border-transparent bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'warning' => 'border-transparent bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
    'ghost' => 'border-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500 dark:text-white dark:hover:bg-gray-800'
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs gap-x-1',
    'sm' => 'px-3 py-2 text-sm gap-x-1.5',
    'md' => 'px-4 py-2.5 text-sm gap-x-2',
    'lg' => 'px-5 py-3 text-base gap-x-2',
    'xl' => 'px-6 py-3.5 text-base gap-x-2.5'
];

$classes = collect([$baseClasses, $variants[$variant], $sizes[$size]])->join(' ');
@endphp

<{{ $attributes->has('href') ? 'a' : 'button' }} 
    {{ $attributes->merge([
        'type' => $attributes->has('href') ? null : $type,
        'class' => $classes,
        'disabled' => $disabled || $loading
    ]) }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif($icon && $iconPosition === 'left')
        <x-icon :name="$icon" class="size-4" />
    @endif
    
    <span>{{ $slot }}</span>
    
    @if($icon && $iconPosition === 'right')
        <x-icon :name="$icon" class="size-4" />
    @endif
    
</{{ $attributes->has('href') ? 'a' : 'button' }}>
