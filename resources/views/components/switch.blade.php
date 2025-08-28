@php
    $label = $attributes->get('label', null);
    $description = $attributes->get('description', null);
    $checked = $attributes->get('checked', false);
    $disabled = $attributes->get('disabled', false);
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'primary');
    $attributes = $attributes->except(['label', 'description', 'checked', 'disabled', 'size', 'variant']);

    $switchId = $attributes->get('id', 'switch-' . uniqid());
    $name = $attributes->get('name', $switchId);

    // Size variants
    $sizeClasses = [
        'sm' => 'w-8 h-4',
        'md' => 'w-11 h-6',
        'lg' => 'w-14 h-7'
    ];

    // Variant colors
    $variantClasses = [
        'primary' => [
            'checked' => 'bg-blue-600',
            'unchecked' => 'bg-gray-200',
            'thumb' => 'bg-white'
        ],
        'success' => [
            'checked' => 'bg-green-600',
            'unchecked' => 'bg-gray-200',
            'thumb' => 'bg-white'
        ],
        'danger' => [
            'checked' => 'bg-red-600',
            'unchecked' => 'bg-gray-200',
            'thumb' => 'bg-white'
        ],
        'warning' => [
            'checked' => 'bg-yellow-600',
            'unchecked' => 'bg-gray-200',
            'thumb' => 'bg-white'
        ]
    ];

    $classes = ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($variantClasses[$variant]['unchecked'] ?? $variantClasses['primary']['unchecked']);
    $thumbClasses = 'w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 ease-in-out translate-x-0 peer-checked:translate-x-5 peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800';
@endphp

<div class="space-y-2">
    <label
        for="{{ $switchId }}"
        class="inline-flex items-center cursor-pointer {{ $disabled ? 'opacity-50 pointer-events-none' : '' }}"
    >
        <!-- Hidden Checkbox Input -->
        <input
            id="{{ $switchId }}"
            name="{{ $name }}"
            type="checkbox"
            {{ $checked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'sr-only peer']) }}
        />

        <!-- Switch Toggle -->
        <div class="relative inline-flex {{ $classes }} rounded-full peer-checked:{{ $variantClasses[$variant]['checked'] ?? $variantClasses['primary']['checked'] }} transition-colors duration-200">
            <div class="{{ $thumbClasses }}"></div>
        </div>

        <!-- Label and Description -->
        @if($label || $description)
            <div class="ml-3 text-sm">
                @if($label)
                    <span class="font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                @endif
                @if($description)
                    <p class="text-gray-500 dark:text-neutral-400">{{ $description }}</p>
                @endif
            </div>
        @endif
    </label>
</div>
