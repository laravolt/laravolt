@php
    $id = $id ?? 'advanced-select-' . uniqid();
    $name = $name ?? $id;
    $placeholder = $placeholder ?? 'Select option...';
    $value = $value ?? null;
    $options = $options ?? [];
    $multiple = $multiple ?? false;
    $searchable = $searchable ?? true;
    $taggable = $taggable ?? false;
    $clearable = $clearable ?? true;
    $size = $size ?? 'md';
    $disabled = $disabled ?? false;

    $hsSelectConfig = json_encode(array_filter([
        'placeholder' => $placeholder,
        'hasSearch' => $searchable,
        'isAddTagOnEnter' => $taggable,
        'toggleTag' => '<button type="button" aria-expanded="false"></button>',
        'toggleClasses' => 'hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400',
        'dropdownClasses' => 'mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-[80] w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700',
        'optionClasses' => 'py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800',
        'optionTemplate' => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>',
        'extraMarkup' => '<div class="absolute top-1/2 end-3 -translate-y-1/2"><svg class="shrink-0 size-3.5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg></div>',
    ]));
@endphp

<div class="relative" {{ $attributes->except(['options', 'value', 'multiple', 'searchable', 'taggable', 'clearable', 'size', 'disabled']) }}>
    <select
        id="{{ $id }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        data-hs-select='{!! $hsSelectConfig !!}'
        class="hidden"
        {{ $multiple ? 'multiple' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    >
        @if(!$multiple && $clearable)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optKey => $optLabel)
            @if(is_array($optLabel))
                <optgroup label="{{ $optKey }}">
                    @foreach($optLabel as $subKey => $subLabel)
                        <option value="{{ $subKey }}" {{ (is_array($value ?? null) ? in_array($subKey, $value) : ($value == $subKey)) ? 'selected' : '' }}>
                            {{ $subLabel }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{ $optKey }}" {{ (is_array($value ?? null) ? in_array($optKey, $value) : ($value == $optKey)) ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endif
        @endforeach
    </select>
</div>
