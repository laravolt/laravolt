@php
    $variant = $attributes->get('variant', 'text');
    $lines = $attributes->get('lines', 1);
    $width = $attributes->get('width', null);
    $height = $attributes->get('height', null);
    $rounded = $attributes->get('rounded', true);
    $animate = $attributes->get('animate', true);
    $attributes = $attributes->except(['variant', 'lines', 'width', 'height', 'rounded', 'animate']);

    // Base classes
    $baseClasses = 'bg-gray-200 dark:bg-neutral-700';

    if ($rounded) {
        $baseClasses .= ' rounded';
    }

    if ($animate) {
        $baseClasses .= ' animate-pulse';
    }

    // Variant-specific classes
    $variantClasses = [
        'text' => $baseClasses . ' h-4',
        'title' => $baseClasses . ' h-6 rounded-md',
        'avatar' => $baseClasses . ' h-10 w-10 rounded-full',
        'button' => $baseClasses . ' h-10 w-20 rounded-md',
        'card' => $baseClasses . ' h-32 rounded-lg',
        'image' => $baseClasses . ' h-48 w-full rounded-lg',
        'rectangle' => $baseClasses . ' rounded-lg',
        'circle' => $baseClasses . ' rounded-full'
    ];

    $classes = $variantClasses[$variant] ?? $variantClasses['text'];

    // Apply custom dimensions
    if ($width) {
        $classes .= ' w-' . $width;
    }

    if ($height) {
        $classes .= ' h-' . $height;
    }
@endphp

@if($variant === 'text' && $lines > 1)
    <div class="space-y-2">
        @for($i = 0; $i < $lines; $i++)
            @php
                $lineWidth = $i === $lines - 1 ? 'w-3/4' : 'w-full';
            @endphp
            <div class="{{ $classes }} {{ $lineWidth }}"></div>
        @endfor
    </div>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}></div>
@endif
