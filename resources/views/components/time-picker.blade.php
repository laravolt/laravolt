@php
    $label = $attributes->get('label', null);
    $placeholder = $attributes->get('placeholder', 'Select time');
    $format = $attributes->get('format', '24h');
    $disabled = $attributes->get('disabled', false);
    $error = $attributes->get('error', null);
    $size = $attributes->get('size', 'md');
    $clearable = $attributes->get('clearable', true);
    $id = $attributes->get('id', 'timepicker-' . uniqid());
    $attributes = $attributes->except(['label', 'placeholder', 'format', 'disabled', 'error', 'size', 'clearable', 'id']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => 'py-2 px-3 text-sm',
        'md' => 'py-3 px-4 text-sm', 
        'lg' => 'py-3.5 px-4 text-base'
    ];

    $inputClasses = 'block w-full border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
    
    if ($error) {
        $inputClasses = str_replace(['focus:border-blue-500', 'focus:ring-blue-500'], ['focus:border-red-500', 'focus:ring-red-500'], $inputClasses) . ' border-red-500 dark:border-red-600';
    }
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
            {{ $label }}
        </label>
    @endif

    <!-- Preline UI v3.0 TimePicker Component -->
    <div class="relative">
        <input
            id="{{ $id }}"
            type="text"
            {{ $attributes->merge([
                'class' => $inputClasses,
                'placeholder' => $placeholder,
                'disabled' => $disabled,
                'data-hs-timepicker' => json_encode([
                    'format' => $format,
                    'clearable' => $clearable
                ])
            ]) }}
            readonly
        >
        
        <!-- Time picker icon -->
        <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none">
            <svg class="shrink-0 size-4 text-gray-400 dark:text-neutral-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12,6 12,12 16,14"></polyline>
            </svg>
        </div>
    </div>

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>

@pushOnce('timepicker-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI TimePickers
    if (window.HSTimepicker) {
        window.HSTimepicker.autoInit();
    }
});
</script>
@endPushOnce