@php
    $span = $attributes->get('span', 'auto');
    $start = $attributes->get('start', null);
    $end = $attributes->get('end', null);
    $attributes = $attributes->except(['span', 'start', 'end']);

    // Column span utilities
    $colSpan = match($span) {
        1 => 'col-span-1',
        2 => 'col-span-2',
        3 => 'col-span-3',
        4 => 'col-span-4',
        5 => 'col-span-5',
        6 => 'col-span-6',
        12 => 'col-span-12',
        'full' => 'col-span-full',
        'auto' => '',
        default => ''
    };

    // Grid column positioning
    $colStart = $start ? "col-start-{$start}" : '';
    $colEnd = $end ? "col-end-{$end}" : '';

    $colClasses = trim("{$colSpan} {$colStart} {$colEnd}");
@endphp

<div {{ $attributes->merge(['class' => $colClasses]) }}>
    {{ $slot }}
</div>
