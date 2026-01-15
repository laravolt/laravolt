@php
    $justify = $attributes->get('justify', 'start');
    $align = $attributes->get('align', 'stretch');
    $gap = $attributes->get('gap', '4');
    $wrap = $attributes->get('wrap', true);
    $attributes = $attributes->except(['justify', 'align', 'gap', 'wrap']);

    $justifyClasses = match($justify) {
        'start' => 'justify-start',
        'center' => 'justify-center',
        'end' => 'justify-end',
        'between' => 'justify-between',
        'around' => 'justify-around',
        'evenly' => 'justify-evenly',
        default => 'justify-start'
    };

    $alignClasses = match($align) {
        'start' => 'items-start',
        'center' => 'items-center',
        'end' => 'items-end',
        'baseline' => 'items-baseline',
        'stretch' => 'items-stretch',
        default => 'items-stretch'
    };

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

    $flexWrap = $wrap ? 'flex-wrap' : 'flex-nowrap';

    $rowClasses = "flex {$justifyClasses} {$alignClasses} {$gapClasses} {$flexWrap}";
@endphp

<div {{ $attributes->merge(['class' => $rowClasses]) }}>
    {{ $slot }}
</div>
