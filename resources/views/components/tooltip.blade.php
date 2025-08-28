@php
    $placement = $attributes->get('placement', 'top');
    $content = $attributes->get('content', '');
    $trigger = $attributes->get('trigger', 'hover');
    $attributes = $attributes->except(['placement', 'content', 'trigger']);

    // Placement classes for tooltip positioning
    $placementClasses = [
        'top' => 'bottom-full mb-2',
        'top-start' => 'bottom-full left-0 mb-2',
        'top-end' => 'bottom-full right-0 mb-2',
        'bottom' => 'top-full mt-2',
        'bottom-start' => 'top-full left-0 mt-2',
        'bottom-end' => 'top-full right-0 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'left-start' => 'right-full top-0 mr-2',
        'left-end' => 'right-full bottom-0 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
        'right-start' => 'left-full top-0 ml-2',
        'right-end' => 'left-full bottom-0 ml-2'
    ];

    $tooltipId = 'tooltip-' . uniqid();
@endphp

<!-- Tooltip Container -->
<div
    class="hs-tooltip [--placement:{{ $placement }}] relative inline-block"
    @if($trigger === 'click') [--trigger:click] @endif
>
    <!-- Trigger Element -->
    <div
        class="hs-tooltip-toggle cursor-help"
        aria-describedby="{{ $tooltipId }}"
    >
        {{ $trigger ?? $slot }}
    </div>

    <!-- Tooltip Content -->
    <div
        id="{{ $tooltipId }}"
        class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 inline-block absolute invisible z-20 py-1.5 px-2.5 bg-gray-900 text-xs text-white rounded-lg shadow-lg transition-opacity duration-200 dark:bg-neutral-700 {{ $placementClasses[$placement] ?? $placementClasses['top'] }}"
        role="tooltip"
    >
        @if($content)
            {{ $content }}
        @else
            {{ $slot }}
        @endif

        <!-- Arrow -->
        @php
            $arrowClasses = match($placement) {
                'top', 'top-start', 'top-end' => 'absolute top-full left-1/2 -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-neutral-700',
                'bottom', 'bottom-start', 'bottom-end' => 'absolute bottom-full left-1/2 -translate-x-1/2 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-900 dark:border-b-neutral-700',
                'left', 'left-start', 'left-end' => 'absolute left-full top-1/2 -translate-y-1/2 border-t-4 border-b-4 border-l-4 border-transparent border-l-gray-900 dark:border-l-neutral-700',
                'right', 'right-start', 'right-end' => 'absolute right-full top-1/2 -translate-y-1/2 border-t-4 border-b-4 border-r-4 border-transparent border-r-gray-900 dark:border-r-neutral-700',
                default => 'absolute top-full left-1/2 -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-neutral-700'
            };
        @endphp
        <div class="{{ $arrowClasses }}"></div>
    </div>
</div>

{{-- Example usage with icon --}}
@unless($slot)
    <button
        type="button"
        class="hs-tooltip-toggle inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 rounded-full focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:text-neutral-500 dark:hover:text-neutral-300"
        aria-describedby="{{ $tooltipId }}"
    >
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>

    <div
        id="{{ $tooltipId }}"
        class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 inline-block absolute invisible z-20 py-1.5 px-2.5 bg-gray-900 text-xs text-white rounded-lg shadow-lg transition-opacity duration-200 dark:bg-neutral-700 {{ $placementClasses[$placement] ?? $placementClasses['top'] }}"
        role="tooltip"
    >
        {{ $content ?? 'This is a tooltip' }}

        <!-- Arrow -->
        <div class="{{ $arrowClasses }}"></div>
    </div>
@endunless
