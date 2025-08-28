@php
    $placement = $attributes->get('placement', 'bottom-left');
    $trigger = $attributes->get('trigger', 'click');
    $offset = $attributes->get('offset', '0');
    $attributes = $attributes->except(['placement', 'trigger', 'offset']);

    // Placement classes for dropdown positioning
    $placementClasses = [
        'top-left' => 'bottom-full mb-2',
        'top-right' => 'bottom-full right-0 mb-2',
        'bottom-left' => 'top-full mt-2',
        'bottom-right' => 'top-full right-0 mt-2',
        'left-top' => 'right-full top-0 mr-2',
        'left-bottom' => 'right-full bottom-0 mr-2',
        'right-top' => 'left-full top-0 ml-2',
        'right-bottom' => 'left-full bottom-0 ml-2'
    ];

    $dropdownId = 'dropdown-' . uniqid();
    $menuClasses = 'hs-dropdown-menu hs-dropdown-open:opacity-100 opacity-0 w-56 transition-[opacity,margin] duration-200 hidden z-50 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-neutral-900 dark:border-neutral-700 ' . ($placementClasses[$placement] ?? $placementClasses['bottom-left']);
@endphp

<!-- Dropdown Container -->
<div
    class="hs-dropdown [--placement:{{ $placement }}] [--offset:{{ $offset }}] relative inline-block"
    @if($trigger === 'hover') [--trigger:hover] @endif
>
    <!-- Trigger -->
    <div {{ $attributes->merge(['class' => 'hs-dropdown-toggle cursor-pointer']) }}>
        {{ $trigger ?? $slot }}
    </div>

    <!-- Dropdown Menu -->
    <div
        id="{{ $dropdownId }}"
        class="{{ $menuClasses }}"
        role="menu"
        aria-labelledby="{{ $dropdownId }}-trigger"
    >
        @if($header ?? false)
            <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                <h6 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $header }}</h6>
                @if($headerDescription ?? false)
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">{{ $headerDescription }}</p>
                @endif
            </div>
        @endif

        <div class="py-2">
            {{ $menu ?? $slot }}
        </div>

        @if($footer ?? false)
            <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
                {!! $footer !!}
            </div>
        @endif
    </div>
</div>

{{-- Example usage with menu items --}}
@unless($slot)
    <button
        id="{{ $dropdownId }}-trigger"
        type="button"
        class="hs-dropdown-toggle inline-flex items-center gap-x-2 py-2 px-3 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700"
    >
        {{ $label ?? 'Dropdown' }}
        <svg class="hs-dropdown-open:rotate-180 transition-transform duration-200 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 opacity-0 w-56 transition-[opacity,margin] duration-200 hidden z-50 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-neutral-900 dark:border-neutral-700 top-full mt-2">
        <div class="py-2">
            @if($items ?? false)
                @foreach($items as $item)
                    @if($item['divider'] ?? false)
                        <div class="border-t border-gray-200 dark:border-neutral-700 my-2"></div>
                    @else
                        <a
                            href="{{ $item['url'] ?? '#' }}"
                            class="flex items-center gap-x-3 py-2 px-4 text-sm text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            @if($item['disabled'] ?? false) aria-disabled="true" @endif
                        >
                            @if($item['icon'] ?? false)
                                <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                            @endif
                            <span>{{ $item['label'] }}</span>
                            @if($item['badge'] ?? false)
                                <span class="ml-auto inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-400">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endunless
