@php
    $label = $attributes->get('label', null);
    $description = $attributes->get('description', null);
    $checked = $attributes->get('checked', false);
    $disabled = $attributes->get('disabled', false);
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'primary');
    $labelPosition = $attributes->get('label-position', 'end');
    $attributes = $attributes->except(['label', 'description', 'checked', 'disabled', 'size', 'variant', 'label-position']);

    $switchId = $attributes->get('id', 'switch-' . uniqid());
    $name = $attributes->get('name', $switchId);

    // Enhanced size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => [
            'container' => 'w-9 h-5',
            'thumb' => 'size-4',
            'translate' => 'translate-x-4'
        ],
        'md' => [
            'container' => 'w-11 h-6', 
            'thumb' => 'size-5',
            'translate' => 'translate-x-5'
        ],
        'lg' => [
            'container' => 'w-14 h-7',
            'thumb' => 'size-6',
            'translate' => 'translate-x-7'
        ]
    ];

    // Updated variant colors for Preline UI v3.0
    $variantClasses = [
        'primary' => 'data-[checked]:bg-blue-600 dark:data-[checked]:bg-blue-500',
        'success' => 'data-[checked]:bg-teal-600 dark:data-[checked]:bg-teal-500',
        'warning' => 'data-[checked]:bg-yellow-500 dark:data-[checked]:bg-yellow-400',
        'danger' => 'data-[checked]:bg-red-600 dark:data-[checked]:bg-red-500',
        'gray' => 'data-[checked]:bg-gray-800 dark:data-[checked]:bg-gray-600'
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentVariant = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<!-- Preline UI v3.0 Switch Component -->
<div class="flex {{ $labelPosition === 'start' ? 'flex-row-reverse' : '' }} items-center {{ $disabled ? 'opacity-50' : '' }}">
    @if($label && $labelPosition === 'start')
        <div class="me-3">
            <label for="{{ $switchId }}" class="text-sm font-medium text-gray-900 dark:text-white cursor-pointer">
                {{ $label }}
            </label>
            @if($description)
                <p class="text-xs text-gray-500 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>
    @endif

    <!-- Switch Toggle Button -->
    <button
        type="button"
        id="{{ $switchId }}"
        class="relative inline-flex shrink-0 {{ $currentSize['container'] }} bg-gray-100 {{ $currentVariant }} rounded-full cursor-pointer transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-neutral-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:pointer-events-none"
        role="switch"
        aria-checked="{{ $checked ? 'true' : 'false' }}"
        aria-labelledby="{{ $switchId }}-label"
        @if($disabled) disabled @endif
        data-hs-toggle-switch='{
            "checked": {{ $checked ? 'true' : 'false' }}
        }'
    >
        <span class="sr-only">{{ $label ?: 'Toggle switch' }}</span>
        
        <!-- Switch Thumb -->
        <span 
            class="size-4 bg-white rounded-full shadow-lg transform transition-transform duration-200 ease-in-out {{ $checked ? $currentSize['translate'] : 'translate-x-0.5' }} dark:bg-neutral-400"
            aria-hidden="true"
        ></span>
    </button>

    <!-- Hidden Input for Form Submission -->
    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ $checked ? '1' : '0' }}"
        {{ $attributes }}
    >

    @if($label && $labelPosition === 'end')
        <div class="ms-3">
            <label for="{{ $switchId }}" id="{{ $switchId }}-label" class="text-sm font-medium text-gray-900 dark:text-white cursor-pointer">
                {{ $label }}
            </label>
            @if($description)
                <p class="text-xs text-gray-500 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>

@pushOnce('switch-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI switches
    document.querySelectorAll('[data-hs-toggle-switch]').forEach(function(el) {
        el.addEventListener('click', function() {
            const isChecked = this.getAttribute('aria-checked') === 'true';
            const newState = !isChecked;
            
            this.setAttribute('aria-checked', newState);
            this.setAttribute('data-checked', newState);
            
            const thumb = this.querySelector('span[aria-hidden="true"]');
            const hiddenInput = this.parentNode.querySelector('input[type="hidden"]');
            
            if (newState) {
                thumb.classList.add('{{ $currentSize["translate"] }}');
                thumb.classList.remove('translate-x-0.5');
                if (hiddenInput) hiddenInput.value = '1';
            } else {
                thumb.classList.remove('{{ $currentSize["translate"] }}');
                thumb.classList.add('translate-x-0.5');
                if (hiddenInput) hiddenInput.value = '0';
            }
            
            // Trigger change event
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
});
</script>
@endPushOnce