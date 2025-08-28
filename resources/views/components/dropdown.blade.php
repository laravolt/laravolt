@php
    $placement = $attributes->get('placement', 'bottom-start');
    $trigger = $attributes->get('trigger', 'click');
    $offset = $attributes->get('offset', 6);
    $strategy = $attributes->get('strategy', 'fixed');
    $autoClose = $attributes->get('auto-close', true);
    $id = $attributes->get('id', 'dropdown-' . uniqid());
    $attributes = $attributes->except(['placement', 'trigger', 'offset', 'strategy', 'auto-close', 'id']);

    // Enhanced placement options for Preline UI v3.0 with Floating UI
    $placementOptions = [
        'top' => 'top',
        'top-start' => 'top-start', 
        'top-end' => 'top-end',
        'right' => 'right',
        'right-start' => 'right-start',
        'right-end' => 'right-end',
        'bottom' => 'bottom',
        'bottom-start' => 'bottom-start',
        'bottom-end' => 'bottom-end',
        'left' => 'left',
        'left-start' => 'left-start',
        'left-end' => 'left-end'
    ];

    $validPlacement = $placementOptions[$placement] ?? $placementOptions['bottom-start'];
    
    // Menu classes with Preline UI v3.0 styling
    $menuClasses = 'hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white shadow-md rounded-lg p-1 space-y-0.5 mt-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700';
@endphp

<!-- Preline UI v3.0 Dropdown with Floating UI -->
<div 
    class="hs-dropdown [--placement:{{ $validPlacement }}] [--strategy:{{ $strategy }}] [--adaptive:adaptive] [--offset:{{ $offset }}] relative inline-flex"
    @if($trigger === 'hover') [--trigger:hover] @endif
    @if(!$autoClose) [--auto-close:false] @endif
>
    <!-- Trigger Button -->
    @if($triggerSlot ?? false)
        <div class="hs-dropdown-toggle">
            {{ $triggerSlot }}
        </div>
    @else
        {{ $trigger ?? '' }}
    @endif

    <!-- Dropdown Menu -->
    <div 
        id="{{ $id }}"
        class="{{ $menuClasses }}"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="hs-dropdown-default"
    >
        @if($header ?? false)
            <div class="py-2 first:pt-0 last:pb-0">
                <span class="block py-2 px-3 text-xs font-medium uppercase text-gray-400 dark:text-neutral-600">
                    {{ $header }}
                </span>
                @if($headerDescription ?? false)
                    <p class="px-3 text-xs text-gray-500 dark:text-neutral-400">{{ $headerDescription }}</p>
                @endif
            </div>
        @endif

        <!-- Menu Items -->
        {{ $slot }}

        @if($footer ?? false)
            <div class="py-2 first:pt-0 last:pb-0 border-t border-gray-200 dark:border-neutral-700">
                {!! $footer !!}
            </div>
        @endif
    </div>
</div>

{{-- Dropdown Item Component for easier usage --}}
@pushOnce('dropdown-item-styles')
<style>
.dropdown-item {
    @apply flex items-center gap-x-2 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700;
}
.dropdown-item.disabled {
    @apply opacity-50 cursor-not-allowed pointer-events-none;
}
.dropdown-divider {
    @apply h-px my-1 bg-gray-200 border-0 dark:bg-neutral-700;
}
</style>
@endPushOnce
