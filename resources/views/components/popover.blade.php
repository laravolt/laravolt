@php
    $placement = $attributes->get('placement', 'bottom');
    $trigger = $attributes->get('trigger', 'click');
    $content = $attributes->get('content', '');
    $attributes = $attributes->except(['placement', 'content', 'trigger']);

    // Placement classes for popover positioning
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

    $popoverId = 'popover-' . uniqid();
@endphp

<!-- Popover Container -->
<div
    class="hs-popover [--placement:{{ $placement }}] [--trigger:{{ $trigger === 'hover' ? 'hover' : 'click' }}] relative inline-block"
>
    <!-- Trigger Element -->
    <div class="hs-popover-toggle cursor-pointer">
        {{ $trigger ?? $slot }}
    </div>

    <!-- Popover Content -->
    <div
        id="{{ $popoverId }}"
        class="hs-popover-content hs-popover-shown:opacity-100 hs-popover-shown:visible opacity-0 inline-block absolute invisible z-50 w-72 p-4 bg-white border border-gray-200 rounded-xl shadow-xl transition-opacity duration-200 dark:bg-neutral-900 dark:border-neutral-700 {{ $placementClasses[$placement] ?? $placementClasses['bottom'] }}"
    >
        @if($content)
            {!! $content !!}
        @else
            {{ $slot }}
        @endif

        <!-- Arrow -->
        @php
            $arrowClasses = match($placement) {
                'top', 'top-start', 'top-end' => 'absolute top-full left-1/2 -translate-x-1/2 border-l-8 border-r-8 border-t-8 border-transparent border-t-white dark:border-t-neutral-900',
                'bottom', 'bottom-start', 'bottom-end' => 'absolute bottom-full left-1/2 -translate-x-1/2 border-l-8 border-r-8 border-b-8 border-transparent border-b-white dark:border-b-neutral-900',
                'left', 'left-start', 'left-end' => 'absolute left-full top-1/2 -translate-y-1/2 border-t-8 border-b-8 border-l-8 border-transparent border-l-white dark:border-l-neutral-900',
                'right', 'right-start', 'right-end' => 'absolute right-full top-1/2 -translate-y-1/2 border-t-8 border-b-8 border-r-8 border-transparent border-r-white dark:border-r-neutral-900',
                default => 'absolute top-full left-1/2 -translate-x-1/2 border-l-8 border-r-8 border-t-8 border-transparent border-t-white dark:border-t-neutral-900'
            };
        @endphp
        <div class="{{ $arrowClasses }}"></div>
    </div>
</div>
