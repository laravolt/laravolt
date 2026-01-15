@php
    $items = $attributes->get('items', []);
    $allowMultiple = $attributes->get('allow-multiple', false);
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'default');
    $bordered = $attributes->get('bordered', true);
    $flush = $attributes->get('flush', false);
    $id = $attributes->get('id', 'accordion-' . uniqid());
    $attributes = $attributes->except(['items', 'allow-multiple', 'size', 'variant', 'bordered', 'flush', 'id']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-sm',
        'lg' => 'text-base'
    ];

    // Variant styles
    $variantClasses = [
        'default' => [
            'container' => 'bg-white border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700',
            'header' => 'bg-gray-50 dark:bg-neutral-700',
            'content' => 'bg-white dark:bg-neutral-800'
        ],
        'light' => [
            'container' => 'bg-gray-50 border border-gray-200 dark:bg-neutral-900 dark:border-neutral-700',
            'header' => 'bg-white dark:bg-neutral-800',
            'content' => 'bg-gray-50 dark:bg-neutral-900'
        ],
        'shadow' => [
            'container' => 'bg-white shadow-sm border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/10',
            'header' => 'bg-white dark:bg-neutral-800',
            'content' => 'bg-white dark:bg-neutral-800'
        ]
    ];

    $currentVariant = $variantClasses[$variant] ?? $variantClasses['default'];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];

    // Container classes
    $containerClasses = 'hs-accordion-group';
    if ($bordered && !$flush) {
        $containerClasses .= ' ' . $currentVariant['container'] . ' rounded-lg overflow-hidden';
    } elseif ($flush) {
        $containerClasses .= ' space-y-px';
    } else {
        $containerClasses .= ' space-y-3';
    }
@endphp

<!-- Preline UI v3.0 Accordion Component -->
<div 
    id="{{ $id }}"
    class="{{ $containerClasses }}"
    @if(!$allowMultiple) data-hs-accordion='{"mode": "single"}' @endif
>
    @if($slot->isNotEmpty())
        {{ $slot }}
    @else
        @foreach($items as $index => $item)
            @php
                $itemId = $id . '-item-' . $index;
                $isOpen = $item['open'] ?? false;
            @endphp
            
            <div class="hs-accordion {{ $isOpen ? 'hs-accordion-active:bg-gray-100 dark:hs-accordion-active:bg-neutral-700' : '' }} {{ $flush ? 'border-b border-gray-200 dark:border-neutral-700 last:border-b-0' : ($bordered ? '' : 'border border-gray-200 dark:border-neutral-700 rounded-lg') }}" id="{{ $itemId }}">
                <!-- Accordion Header -->
                <button 
                    class="hs-accordion-toggle {{ $flush ? 'py-4' : 'p-5' }} inline-flex items-center justify-between gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-200 dark:hover:text-neutral-400 dark:focus:text-neutral-400 {{ $currentSize }}"
                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                    aria-controls="{{ $itemId }}-content"
                    @if($isOpen) aria-expanded="true" @endif
                >
                    {{ $item['title'] ?? 'Accordion Item' }}
                    
                    <!-- Chevron Icon -->
                    <svg class="hs-accordion-active:hidden block shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                    <svg class="hs-accordion-active:block hidden shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 15-6-6-6 6"></path>
                    </svg>
                </button>
                
                <!-- Accordion Content -->
                <div 
                    id="{{ $itemId }}-content"
                    class="hs-accordion-content {{ $isOpen ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300"
                    role="region"
                    aria-labelledby="{{ $itemId }}"
                >
                    <div class="{{ $flush ? 'pb-4' : 'p-5 pt-0' }}">
                        @if(isset($item['content']))
                            {!! $item['content'] !!}
                        @else
                            <p class="text-gray-600 dark:text-neutral-400">
                                {{ $item['description'] ?? 'Accordion content goes here.' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@pushOnce('accordion-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI Accordion
    if (window.HSAccordion) {
        window.HSAccordion.autoInit();
    }
});
</script>
@endPushOnce