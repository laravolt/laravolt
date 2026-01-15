@php
    $items = $attributes->get('items', []);
    $orientation = $attributes->get('orientation', 'vertical');
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'default');
    $showConnector = $attributes->get('show-connector', true);
    $showIcons = $attributes->get('show-icons', true);
    $attributes = $attributes->except(['items', 'orientation', 'size', 'variant', 'show-connector', 'show-icons']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => [
            'icon' => 'size-6',
            'text' => 'text-sm',
            'spacing' => 'gap-3'
        ],
        'md' => [
            'icon' => 'size-8',
            'text' => 'text-sm',
            'spacing' => 'gap-4'
        ],
        'lg' => [
            'icon' => 'size-10',
            'text' => 'text-base',
            'spacing' => 'gap-6'
        ]
    ];

    // Variant styles
    $variantClasses = [
        'default' => [
            'connector' => 'border-gray-200 dark:border-neutral-700',
            'icon_bg' => 'bg-gray-50 dark:bg-neutral-800',
            'icon_border' => 'border-gray-200 dark:border-neutral-700',
            'icon_text' => 'text-gray-600 dark:text-neutral-400'
        ],
        'primary' => [
            'connector' => 'border-blue-200 dark:border-blue-800',
            'icon_bg' => 'bg-blue-50 dark:bg-blue-900',
            'icon_border' => 'border-blue-200 dark:border-blue-800',
            'icon_text' => 'text-blue-600 dark:text-blue-400'
        ],
        'success' => [
            'connector' => 'border-teal-200 dark:border-teal-800',
            'icon_bg' => 'bg-teal-50 dark:bg-teal-900',
            'icon_border' => 'border-teal-200 dark:border-teal-800',
            'icon_text' => 'text-teal-600 dark:text-teal-400'
        ]
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentVariant = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<!-- Preline UI v3.0 Timeline Component -->
<div {{ $attributes->merge(['class' => 'space-y-6']) }}>
    @if($slot->isNotEmpty())
        {{ $slot }}
    @else
        @foreach($items as $index => $item)
            @php
                $isLast = $index === count($items) - 1;
                $status = $item['status'] ?? 'default';
                $icon = $item['icon'] ?? null;
                $timestamp = $item['timestamp'] ?? null;
                $title = $item['title'] ?? '';
                $description = $item['description'] ?? '';
                $content = $item['content'] ?? null;
            @endphp
            
            <div class="relative flex {{ $currentSize['spacing'] }}">
                @if($showIcons)
                    <!-- Timeline Icon -->
                    <div class="relative flex justify-center">
                        @if($showConnector && !$isLast)
                            <!-- Connector Line -->
                            <div class="absolute top-8 start-4 w-px h-full {{ $currentVariant['connector'] }} border-s"></div>
                        @endif
                        
                        <!-- Icon Container -->
                        <div class="relative z-10 flex justify-center items-center {{ $currentSize['icon'] }} {{ $currentVariant['icon_bg'] }} border-2 {{ $currentVariant['icon_border'] }} rounded-full">
                            @if($icon)
                                @if(str_contains($icon, '<svg'))
                                    <div class="{{ $currentVariant['icon_text'] }}">
                                        {!! $icon !!}
                                    </div>
                                @else
                                    <svg class="shrink-0 size-4 {{ $currentVariant['icon_text'] }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <use xlink:href="#{{ $icon }}"></use>
                                    </svg>
                                @endif
                            @else
                                <!-- Default Circle -->
                                <div class="size-2 {{ $currentVariant['icon_text'] }} bg-current rounded-full"></div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Timeline Content -->
                <div class="grow pb-8 {{ $currentSize['text'] }}">
                    @if($timestamp)
                        <span class="block text-xs font-medium text-gray-500 dark:text-neutral-500 uppercase tracking-wide">
                            {{ $timestamp }}
                        </span>
                    @endif
                    
                    @if($title)
                        <h3 class="flex gap-x-1.5 font-semibold text-gray-800 dark:text-white">
                            {{ $title }}
                            @if($status === 'completed')
                                <svg class="shrink-0 size-4 text-teal-600 dark:text-teal-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                            @elseif($status === 'pending')
                                <svg class="shrink-0 size-4 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12,6 12,12 16,14"></polyline>
                                </svg>
                            @endif
                        </h3>
                    @endif
                    
                    @if($description)
                        <p class="mt-1 text-gray-600 dark:text-neutral-400">
                            {{ $description }}
                        </p>
                    @endif
                    
                    @if($content)
                        <div class="mt-2">
                            {!! $content !!}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>