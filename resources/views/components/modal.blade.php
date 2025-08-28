@php
    $size = $attributes->get('size', 'md');
    $backdrop = $attributes->get('backdrop', true);
    $static = $attributes->get('static', false);
    $centered = $attributes->get('centered', true);
    $scrollable = $attributes->get('scrollable', false);
    $id = $attributes->get('id', 'modal-' . uniqid());
    $attributes = $attributes->except(['size', 'backdrop', 'static', 'centered', 'scrollable', 'id']);

    // Enhanced size classes for Preline UI v3.0
    $sizeClasses = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md', 
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full'
    ];

    // Modal positioning
    $positionClasses = $centered ? 'items-center' : 'items-start pt-16';
    $scrollClasses = $scrollable ? 'max-h-full overflow-y-auto' : '';
@endphp

<!-- Preline UI v3.0 Modal with Floating UI integration -->
<div
    id="{{ $id }}"
    class="hs-overlay hs-overlay-backdrop-open:bg-gray-900/50 hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none [--overlay-backdrop:static] [--body-scroll:true]"
    role="dialog"
    tabindex="-1"
    aria-labelledby="{{ $id }}-label"
    @if($static) data-hs-overlay-keyboard="false" @endif
>
    <div class="hs-overlay-animation-target hs-overlay-open:scale-100 hs-overlay-open:opacity-100 scale-95 opacity-0 ease-in-out transition-all duration-200 {{ $positionClasses }} justify-center min-h-full p-4">
        <div
            {{ $attributes->merge([
                'class' => 'w-full ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70 ' . $scrollClasses
            ]) }}
        >
            <!-- Header -->
            @if($header ?? false)
                <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                    <h3 id="{{ $id }}-label" class="font-bold text-gray-800 dark:text-white">
                        {{ $header }}
                    </h3>
                    <button 
                        type="button" 
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" 
                        aria-label="Close" 
                        data-hs-overlay="#{{ $id }}"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 6-12 12"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
            @else
                <!-- Close button when no header -->
                <div class="absolute top-2 end-2 z-10">
                    <button 
                        type="button" 
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" 
                        aria-label="Close" 
                        data-hs-overlay="#{{ $id }}"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 6-12 12"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Body -->
            <div class="p-4 overflow-y-auto">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if($footer ?? false)
                <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
