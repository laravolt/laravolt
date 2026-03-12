@php
    $id = $attributes->get('id', 'input-number-' . uniqid());
    $name = $attributes->get('name', $id);
    $value = $attributes->get('value', 0);
    $min = $attributes->get('min', null);
    $max = $attributes->get('max', null);
    $step = $attributes->get('step', 1);
    $size = $attributes->get('size', 'md');
    $disabled = $attributes->get('disabled', false);
    $prefix = $attributes->get('prefix', null);
    $suffix = $attributes->get('suffix', null);
    $hsInputNumberConfig = json_encode(array_filter([
        'min' => $min,
        'max' => $max,
        'step' => $step,
    ], fn($v) => $v !== null));
@endphp

<div class="inline-flex items-center" data-hs-input-number='{!! $hsInputNumberConfig !!}' {{ $attributes->except(['value', 'min', 'max', 'step', 'size', 'disabled', 'prefix', 'suffix']) }}>
    <div class="flex items-center gap-x-1.5">
        <button type="button"
                class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                aria-label="Decrease"
                data-hs-input-number-decrement=""
                {{ $disabled ? 'disabled' : '' }}>
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
        </button>

        <div class="flex items-center gap-x-1">
            @if($prefix)
                <span class="text-sm text-gray-500 dark:text-neutral-400">{{ $prefix }}</span>
            @endif
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                class="p-0 w-12 bg-transparent border-0 text-gray-800 text-center focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none dark:text-white"
                style="-moz-appearance:textfield;"
                type="number"
                aria-roledescription="Number field"
                value="{{ $value }}"
                data-hs-input-number-input=""
                {{ $disabled ? 'disabled' : '' }}
                @if($min !== null) min="{{ $min }}" @endif
                @if($max !== null) max="{{ $max }}" @endif
                step="{{ $step }}"
            >
            @if($suffix)
                <span class="text-sm text-gray-500 dark:text-neutral-400">{{ $suffix }}</span>
            @endif
        </div>

        <button type="button"
                class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                aria-label="Increase"
                data-hs-input-number-increment=""
                {{ $disabled ? 'disabled' : '' }}>
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        </button>
    </div>
</div>
