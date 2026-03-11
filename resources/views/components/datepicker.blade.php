@php
    $id = $id ?? 'datepicker-' . uniqid();
    $name = $name ?? $id;
    $value = $value ?? null;
    $placeholder = $placeholder ?? 'Select date';
    $format = $format ?? 'DD.MM.YYYY';
    $min = $min ?? null;
    $max = $max ?? null;
    $range = $range ?? false;
    $disabled = $disabled ?? false;
    $size = $size ?? 'md';
    $hsDatepickerConfig = json_encode(array_filter([
        'dateFormat' => $format,
        'dateMin' => $min,
        'dateMax' => $max,
        'mode' => 'default',
        'inputMode' => true,
    ], fn($v) => $v !== null));
    $sizeClasses = [
        'sm' => 'py-1.5 px-2.5 text-sm',
        'md' => 'py-2 px-3 text-sm',
        'lg' => 'py-2.5 px-4 text-base',
    ];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="relative" {{ $attributes->except(['value', 'format', 'min', 'max', 'range', 'disabled', 'size']) }}>
    <div class="relative">
        <input
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            class="hs-datepicker {{ $currentSize }} ps-10 block w-full border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            data-hs-datepicker='{!! $hsDatepickerConfig !!}'
            {{ $disabled ? 'disabled' : '' }}
            autocomplete="off"
        >
        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
            <svg class="shrink-0 size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
        </div>
    </div>
</div>
