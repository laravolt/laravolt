@php
    $variant = $attributes->get('variant', 'info');
    $dismissible = $attributes->get('dismissible', false);
    $icon = $attributes->get('icon', null);
    $attributes = $attributes->except(['variant', 'dismissible', 'icon']);

    $alertId = 'alert-' . uniqid();

    // Variant styles
    $variantClasses = [
        'success' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900 dark:border-green-800 dark:text-green-200',
        'error' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900 dark:border-red-800 dark:text-red-200',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:border-yellow-800 dark:text-yellow-200',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900 dark:border-blue-800 dark:text-blue-200',
        'gray' => 'bg-gray-50 border-gray-200 text-gray-800 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-200'
    ];

    // Default icons for variants
    $defaultIcons = [
        'success' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'error' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>',
        'warning' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>',
        'info' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'gray' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
    ];

    $classes = 'flex items-start gap-3 p-4 border rounded-lg ' . ($variantClasses[$variant] ?? $variantClasses['info']);
@endphp

<div
    id="{{ $alertId }}"
    {{ $attributes->merge(['class' => $classes, 'role' => 'alert']) }}
>
    <!-- Icon -->
    @if($icon || ($defaultIcons[$variant] ?? false))
        <div class="flex-shrink-0">
            {!! $icon ?: ($defaultIcons[$variant] ?? '') !!}
        </div>
    @endif

    <!-- Content -->
    <div class="flex-1 min-w-0">
        @if($title ?? false)
            <h4 class="text-sm font-semibold mb-1">{{ $title }}</h4>
        @endif

        <div class="text-sm">
            {{ $message ?? $slot }}
        </div>
    </div>

    <!-- Dismiss button -->
    @if($dismissible)
        <button
            type="button"
            class="flex-shrink-0 ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-opacity-20 focus:outline-hidden focus:ring-2 focus:ring-offset-2 transition-colors duration-200"
            onclick="document.getElementById('{{ $alertId }}').style.display = 'none'"
            aria-label="Dismiss"
        >
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
