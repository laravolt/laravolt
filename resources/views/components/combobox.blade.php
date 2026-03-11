@php
    $id = $id ?? 'combobox-' . uniqid();
    $name = $name ?? $id;
    $placeholder = $placeholder ?? 'Type to search...';
    $value = $value ?? null;
    $options = $options ?? [];
    $apiUrl = $apiUrl ?? null;
    $minChars = $minChars ?? 1;
    $disabled = $disabled ?? false;
    $size = $size ?? 'md';
    $groupField = $groupField ?? null;

    $sizeClasses = [
        'sm' => 'py-1.5 px-2.5 text-sm',
        'md' => 'py-2 px-3 text-sm',
        'lg' => 'py-2.5 px-4 text-base',
    ];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $hsComboboxConfig = json_encode(array_filter([
        'preventVisibility' => false,
        'groupingType' => $groupField ? 'default' : null,
        'groupingTitleTemplate' => $groupField ? '<div class="text-xs uppercase text-gray-500 m-3 mb-1 dark:text-neutral-500"></div>' : null,
        'apiUrl' => $apiUrl,
        'minSearchLength' => $minChars,
    ]));
@endphp

<div class="relative" data-hs-combo-box='{!! $hsComboboxConfig !!}' {{ $attributes->except(['options', 'value', 'api-url', 'min-chars', 'disabled', 'size', 'group-field']) }}>
    <div class="relative">
        <input
            id="{{ $id }}"
            class="{{ $currentSize }} pe-9 block w-full border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            type="text"
            role="combobox"
            aria-expanded="false"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            data-hs-combo-box-input=""
            {{ $disabled ? 'disabled' : '' }}
        >
        <div class="absolute top-1/2 end-3 -translate-y-1/2" data-hs-combo-box-toggle="">
            <svg class="shrink-0 size-3.5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
        </div>
    </div>

    <div class="absolute z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:bg-neutral-900 dark:border-neutral-700 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500" style="display:none;" data-hs-combo-box-output="">
        @foreach($options as $optKey => $optLabel)
            <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800"
                 tabindex="0"
                 data-hs-combo-box-output-item=""
                 data-hs-combo-box-search-text="{{ $optLabel }}"
                 data-hs-combo-box-value="{{ $optKey }}"
                 @if($groupField) data-hs-combo-box-group-val="{{ data_get($optLabel, $groupField, '') }}" @endif
            >
                <div class="flex justify-between items-center w-full">
                    <span data-hs-combo-box-search-text="{{ is_string($optLabel) ? $optLabel : $optKey }}" data-hs-combo-box-value="{{ $optKey }}">
                        {{ is_string($optLabel) ? $optLabel : $optKey }}
                    </span>
                    <span class="hidden hs-combo-box-selected:block">
                        <svg class="shrink-0 size-3.5 text-blue-600 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                </div>
            </div>
        @endforeach
        <div class="cursor-default py-2 px-4 w-full text-sm text-gray-400 dark:text-neutral-500" data-hs-combo-box-output-item-not-found="" style="display:none;">
            No results found
        </div>
    </div>

    <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-hs-combo-box-value-field="">
</div>
