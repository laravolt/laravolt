@php
    $variant = $attributes->get('variant', 'info');
    $dismissible = $attributes->get('dismissible', false);
    $icon = $attributes->get('icon', null);
    $size = $attributes->get('size', 'md');
    $rounded = $attributes->get('rounded', true);
    $border = $attributes->get('border', true);
    $attributes = $attributes->except(['variant', 'dismissible', 'icon', 'size', 'rounded', 'border']);

    $alertId = 'alert-' . uniqid();

    // Base classes with latest Preline UI patterns
    $baseClasses = 'hs-alert flex items-start gap-x-3 transition-all duration-300';
    
    // Size variants following Preline UI v3.0 patterns
    $sizeClasses = [
        'sm' => 'p-3 text-sm',
        'md' => 'p-4 text-sm',
        'lg' => 'p-6 text-base'
    ];

    // Rounded variants
    $roundedClasses = $rounded ? 'rounded-lg' : '';
    $borderClasses = $border ? 'border' : '';

    // Updated variant styles for Preline UI v3.0 with improved color contrast and modern design
    $variantClasses = [
        'success' => 'bg-teal-50 border-teal-200 text-teal-800 dark:bg-teal-800/10 dark:border-teal-900 dark:text-teal-500',
        'error' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-800/10 dark:border-blue-900 dark:text-blue-500',
        'gray' => 'bg-gray-50 border-gray-200 text-gray-800 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200',
        'light' => 'bg-white border-gray-200 text-gray-800 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200'
    ];

    // Updated icons with better SVG optimization for Preline UI v3.0
    $defaultIcons = [
        'success' => '<svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>',
        'error' => '<svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>',
        'warning' => '<svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>',
        'info' => '<svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>',
        'gray' => '<svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>',
        'light' => '<svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>'
    ];

    $classes = $baseClasses . ' ' . 
                ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . 
                $roundedClasses . ' ' . 
                $borderClasses . ' ' . 
                ($variantClasses[$variant] ?? $variantClasses['info']);
@endphp

<div
    id="{{ $alertId }}"
    {{ $attributes->merge(['class' => $classes, 'role' => 'alert']) }}
>
    <!-- Icon -->
    @if($icon || ($defaultIcons[$variant] ?? false))
        <div class="shrink-0">
            {!! $icon ?: ($defaultIcons[$variant] ?? '') !!}
        </div>
    @endif

    <!-- Content -->
    <div class="flex-1 min-w-0">
        @if($title ?? false)
            <h4 class="text-sm font-semibold mb-1">{{ $title }}</h4>
        @endif

        <div class="text-sm leading-relaxed">
            {{ $message ?? $slot }}
        </div>
    </div>

    <!-- Dismiss button with Preline UI v3.0 styles -->
    @if($dismissible)
        <div class="ps-3">
            <button
                type="button"
                class="hs-remove-element inline-flex bg-teal-50 rounded-lg p-1.5 text-teal-500 hover:bg-teal-100 focus:outline-none focus:bg-teal-100 dark:bg-transparent dark:text-teal-600 dark:hover:bg-teal-800/50 dark:focus:bg-teal-800/50"
                data-hs-remove-element="#{{ $alertId }}"
                aria-label="Dismiss"
            >
                <span class="sr-only">Dismiss</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 6-12 12"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
    @endif
</div>
