@php
    $bordered = $attributes->get('bordered', false);
    $responsive = $attributes->get('responsive', true);
    $attributes = $attributes->except(['bordered', 'responsive', 'striped', 'hover']);

    $tableClasses = 'min-w-full divide-y divide-gray-200 dark:divide-neutral-700';

    if ($bordered) {
        $tableClasses .= ' border border-gray-200 dark:border-neutral-700';
    }
@endphp

@if($responsive)
<div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
@endif

<table {{ $attributes->merge(['class' => $tableClasses]) }} aria-label="table">
    {{ $slot }}
</table>

@if($responsive)
</div>
@endif
