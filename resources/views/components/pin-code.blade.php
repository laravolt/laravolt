@php
    $length = $attributes->get('length', 4);
    $mask = $attributes->get('mask', false);
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'default');
    $disabled = $attributes->get('disabled', false);
    $placeholder = $attributes->get('placeholder', '○');
    $id = $attributes->get('id', 'pincode-' . uniqid());
    $name = $attributes->get('name', $id);
    $attributes = $attributes->except(['length', 'mask', 'size', 'variant', 'disabled', 'placeholder', 'id', 'name']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => 'size-10 text-sm',
        'md' => 'size-12 text-base',
        'lg' => 'size-14 text-lg'
    ];

    // Variant styles
    $variantClasses = [
        'default' => 'border-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:focus:border-blue-500 dark:focus:ring-blue-500',
        'success' => 'border-teal-200 focus:border-teal-500 focus:ring-teal-500 dark:border-teal-700',
        'error' => 'border-red-200 focus:border-red-500 focus:ring-red-500 dark:border-red-700'
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentVariant = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<!-- Preline UI v3.0 PIN Code Component -->
<div class="flex items-center gap-x-2" data-hs-pin-input='{"length": {{ $length }}}'>
    @for($i = 0; $i < $length; $i++)
        <input 
            type="{{ $mask ? 'password' : 'text' }}"
            class="block {{ $currentSize }} text-center border {{ $currentVariant }} rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:text-neutral-400 dark:placeholder-neutral-500"
            data-hs-pin-input-item
            placeholder="{{ $placeholder }}"
            maxlength="1"
            pattern="[0-9]"
            inputmode="numeric"
            {{ $disabled ? 'disabled' : '' }}
        >
    @endfor
    
    <!-- Hidden input for form submission -->
    <input 
        type="hidden" 
        name="{{ $name }}" 
        data-hs-pin-input-target
        {{ $attributes }}
    >
</div>

@pushOnce('pincode-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI PIN Input
    if (window.HSPinInput) {
        window.HSPinInput.autoInit();
    }
    
    // Custom PIN Input functionality
    document.querySelectorAll('[data-hs-pin-input]').forEach(function(container) {
        const inputs = container.querySelectorAll('[data-hs-pin-input-item]');
        const hiddenInput = container.querySelector('[data-hs-pin-input-target]');
        const length = parseInt(container.getAttribute('data-hs-pin-input').match(/"length":\s*(\d+)/)[1]);
        
        inputs.forEach(function(input, index) {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Only allow single digit
                if (value.length > 1) {
                    e.target.value = value.slice(0, 1);
                }
                
                // Update hidden input
                updateHiddenInput();
                
                // Move to next input
                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
                
                // Handle arrow keys
                if (e.key === 'ArrowLeft' && index > 0) {
                    inputs[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/\D/g, '').split('');
                
                digits.forEach(function(digit, i) {
                    if (index + i < inputs.length) {
                        inputs[index + i].value = digit;
                    }
                });
                
                updateHiddenInput();
                
                // Focus next empty input or last input
                const nextIndex = Math.min(index + digits.length, inputs.length - 1);
                inputs[nextIndex].focus();
            });
        });
        
        function updateHiddenInput() {
            const values = Array.from(inputs).map(input => input.value).join('');
            if (hiddenInput) {
                hiddenInput.value = values;
            }
            
            // Trigger change event when complete
            if (values.length === length) {
                container.dispatchEvent(new CustomEvent('pin-complete', {
                    detail: { value: values },
                    bubbles: true
                }));
            }
        }
    });
});
</script>
@endPushOnce