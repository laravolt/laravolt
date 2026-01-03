@php
    $placement = $attributes->get('placement', 'end');
    $show = $attributes->get('show', false);
    $backdrop = $attributes->get('backdrop', true);
    $scrollable = $attributes->get('scrollable', false);
    $attributes = $attributes->except(['placement', 'show', 'backdrop', 'scrollable']);

    $offcanvasId = 'offcanvas-' . uniqid();

    // Placement classes
    $placementClasses = [
        'start' => 'left-0 top-0 h-full w-80 transform -translate-x-full',
        'end' => 'right-0 top-0 h-full w-80 transform translate-x-full',
        'top' => 'top-0 left-0 w-full h-80 transform -translate-y-full',
        'bottom' => 'bottom-0 left-0 w-full h-80 transform translate-y-full'
    ];

    $enterClasses = [
        'start' => 'translate-x-0',
        'end' => 'translate-x-0',
        'top' => 'translate-y-0',
        'bottom' => 'translate-y-0'
    ];

    $containerClasses = 'fixed z-50 bg-white shadow-xl transition-transform duration-300 ease-in-out dark:bg-neutral-900 ' . ($placementClasses[$placement] ?? $placementClasses['end']);

    $scrollClasses = $scrollable ? 'overflow-y-auto' : 'overflow-hidden';
@endphp

<!-- Backdrop -->
@if($backdrop)
    <div
        x-show="{{ $show ? 'true' : 'false' }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70"
        @click="show = false"
        x-data="{ show: {{ $show ? 'true' : 'false' }} }"
    ></div>
@endif

<!-- Offcanvas Panel -->
<div
    id="{{ $offcanvasId }}"
    x-data="{ open: {{ $show ? 'true' : 'false' }} }"
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="{{ $placementClasses[$placement] ?? $placementClasses['end'] }}"
    x-transition:enter-end="{{ $enterClasses[$placement] ?? $enterClasses['end'] }}"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="{{ $enterClasses[$placement] ?? $enterClasses['end'] }}"
    x-transition:leave-end="{{ $placementClasses[$placement] ?? $placementClasses['end'] }}"
    {{ $attributes->merge(['class' => $containerClasses . ' ' . $scrollClasses]) }}
>
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-neutral-700">
        @if($title ?? false)
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $title }}
            </h2>
        @else
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Panel Title
            </h2>
        @endif

        <!-- Close Button -->
        <button
            type="button"
            class="text-gray-400 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-200"
            @click="open = false"
        >
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Content -->
    <div class="p-6 {{ $scrollable ? 'flex-1' : '' }}">
        @if($content ?? false)
            {!! $content !!}
        @else
            {{ $slot ?? 'Offcanvas content goes here...' }}
        @endif
    </div>

    <!-- Footer (optional) -->
    @if($footer ?? false)
        <div class="border-t border-gray-200 p-6 dark:border-neutral-700">
            {!! $footer !!}
        </div>
    @endif
</div>
