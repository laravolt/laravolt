@php
    $cols = $attributes->get('cols', 'auto');
    $gap = $attributes->get('gap', '4');
    $align = $attributes->get('align', 'start');
    $justify = $attributes->get('justify', 'start');
    $attributes = $attributes->except(['cols', 'gap', 'align', 'justify']);

    // CSS Grid columns
    $gridCols = match($cols) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        5 => 'grid-cols-5',
        6 => 'grid-cols-6',
        12 => 'grid-cols-12',
        'auto' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
    };

    // Gap utilities
    $gapClasses = match($gap) {
        1 => 'gap-1',
        2 => 'gap-2',
        3 => 'gap-3',
        4 => 'gap-4',
        5 => 'gap-5',
        6 => 'gap-6',
        8 => 'gap-8',
        default => 'gap-4'
    };

    // Alignment utilities
    $alignClasses = match($align) {
        'start' => 'items-start',
        'center' => 'items-center',
        'end' => 'items-end',
        'stretch' => 'items-stretch',
        'baseline' => 'items-baseline',
        default => 'items-start'
    };

    $justifyClasses = match($justify) {
        'start' => 'justify-items-start',
        'center' => 'justify-items-center',
        'end' => 'justify-items-end',
        'stretch' => 'justify-items-stretch',
        default => 'justify-items-start'
    };

    $gridClasses = "grid {$gridCols} {$gapClasses} {$alignClasses} {$justifyClasses}";
@endphp

<div {{ $attributes->merge(['class' => $gridClasses]) }}>
    {{ $slot }}
</div>
