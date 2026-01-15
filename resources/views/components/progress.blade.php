@php
    $value = $attributes->get('value', 0);
    $max = $attributes->get('max', 100);
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'primary');
    $showLabel = $attributes->get('show-label', false);
    $label = $attributes->get('label', null);
    $attributes = $attributes->except(['value', 'max', 'size', 'variant', 'show-label', 'label']);

    $percentage = $max > 0 ? round(($value / $max) * 100) : 0;

    // Size variants
    $sizeClasses = [
        'sm' => 'h-1.5',
        'md' => 'h-2.5',
        'lg' => 'h-4',
        'xl' => 'h-6'
    ];

    // Variant colors
    $variantClasses = [
        'primary' => 'bg-blue-600',
        'secondary' => 'bg-gray-600',
        'success' => 'bg-green-600',
        'danger' => 'bg-red-600',
        'warning' => 'bg-yellow-600',
        'info' => 'bg-cyan-600'
    ];

    $progressClasses = 'w-full bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700 transition-all duration-300';
    $barClasses = 'h-full transition-all duration-500 ease-out rounded-full ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if($label)
        <div class="flex justify-between items-center text-sm">
            <span class="font-medium text-gray-900 dark:text-white">{{ $label }}</span>
            @if($showLabel)
                <span class="text-gray-500 dark:text-neutral-400">{{ $percentage }}%</span>
            @endif
        </div>
    @endif

    <div class="{{ $progressClasses }} {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}">
        <div
            class="{{ $barClasses }}"
            style="width: {{ $percentage }}%"
            role="progressbar"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
            @if($label) aria-label="{{ $label }}" @endif
        ></div>
    </div>

    @if($showLabel && !$label)
        <div class="text-right text-sm text-gray-500 dark:text-neutral-400">
            {{ $percentage }}%
        </div>
    @endif
</div>
