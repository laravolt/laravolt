@php
    $id = $id ?? 'range-' . uniqid();
    $name = $name ?? $id;
    $value = $value ?? 50;
    $min = $min ?? 0;
    $max = $max ?? 100;
    $step = $step ?? 1;
    $showValue = $showValue ?? true;
    $disabled = $disabled ?? false;
@endphp

<div {{ $attributes->except(['value', 'min', 'max', 'step', 'show-value', 'disabled']) }}>
    <div class="flex items-center gap-x-3">
        @if($showValue)
            <span id="{{ $id }}-display" class="text-sm font-medium text-gray-800 dark:text-white" style="min-width:2.5rem;text-align:right">{{ $value }}</span>
        @endif
        <input
            type="range"
            id="{{ $id }}"
            name="{{ $name }}"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-neutral-700 accent-blue-600"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            value="{{ $value }}"
            {{ $disabled ? 'disabled' : '' }}
            @if($showValue)
            oninput="document.getElementById('{{ $id }}-display').textContent = this.value"
            @endif
        >
        @if($showValue)
            <span class="text-xs text-gray-500 dark:text-neutral-400">/ {{ $max }}</span>
        @endif
    </div>
</div>
