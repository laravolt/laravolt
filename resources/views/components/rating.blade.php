@php
    $value = $attributes->get('value', 0);
    $max = $attributes->get('max', 5);
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'yellow');
    $readonly = $attributes->get('readonly', false);
    $showCount = $attributes->get('show-count', false);
    $count = $attributes->get('count', null);
    $precision = $attributes->get('precision', 1);
    $id = $attributes->get('id', 'rating-' . uniqid());
    $name = $attributes->get('name', $id);
    $attributes = $attributes->except(['value', 'max', 'size', 'variant', 'readonly', 'show-count', 'count', 'precision', 'id', 'name']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'xs' => 'size-3',
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
        'xl' => 'size-8'
    ];

    // Variant colors
    $variantClasses = [
        'yellow' => [
            'filled' => 'text-yellow-400',
            'empty' => 'text-gray-300 dark:text-neutral-600',
            'hover' => 'hover:text-yellow-400'
        ],
        'orange' => [
            'filled' => 'text-orange-400',
            'empty' => 'text-gray-300 dark:text-neutral-600',
            'hover' => 'hover:text-orange-400'
        ],
        'red' => [
            'filled' => 'text-red-400',
            'empty' => 'text-gray-300 dark:text-neutral-600',
            'hover' => 'hover:text-red-400'
        ],
        'blue' => [
            'filled' => 'text-blue-400',
            'empty' => 'text-gray-300 dark:text-neutral-600',
            'hover' => 'hover:text-blue-400'
        ]
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentVariant = $variantClasses[$variant] ?? $variantClasses['yellow'];
    
    // Calculate filled stars
    $filledStars = floor($value);
    $hasHalfStar = $value - $filledStars >= 0.5;
    $emptyStars = $max - $filledStars - ($hasHalfStar ? 1 : 0);
@endphp

<!-- Preline UI v3.0 Rating Component -->
<div class="flex items-center gap-x-1">
    @if(!$readonly)
        <!-- Interactive Rating -->
        <div class="flex items-center" data-hs-rating='{"value": {{ $value }}, "max": {{ $max }}}'>
            @for($i = 1; $i <= $max; $i++)
                <button 
                    type="button"
                    class="hs-rating-star {{ $currentSize }} {{ $i <= $value ? $currentVariant['filled'] : $currentVariant['empty'] }} {{ $currentVariant['hover'] }} transition-colors duration-200"
                    data-hs-rating-value="{{ $i }}"
                >
                    <svg class="shrink-0 size-full" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                </button>
            @endfor
            
            <!-- Hidden input for form submission -->
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" {{ $attributes }}>
        </div>
    @else
        <!-- Read-only Rating -->
        <div class="flex items-center">
            @for($i = 1; $i <= $filledStars; $i++)
                <svg class="shrink-0 {{ $currentSize }} {{ $currentVariant['filled'] }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                </svg>
            @endfor
            
            @if($hasHalfStar)
                <div class="relative">
                    <svg class="shrink-0 {{ $currentSize }} {{ $currentVariant['empty'] }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    <div class="absolute inset-0 overflow-hidden" style="width: 50%;">
                        <svg class="shrink-0 {{ $currentSize }} {{ $currentVariant['filled'] }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </div>
                </div>
            @endif
            
            @for($i = 1; $i <= $emptyStars; $i++)
                <svg class="shrink-0 {{ $currentSize }} {{ $currentVariant['empty'] }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                </svg>
            @endfor
        </div>
    @endif
    
    @if($showCount && $count !== null)
        <span class="text-sm text-gray-500 dark:text-neutral-400 ms-1">
            ({{ number_format($count) }})
        </span>
    @endif
    
    @if($readonly && $precision > 0)
        <span class="text-sm text-gray-500 dark:text-neutral-400 ms-1">
            {{ number_format($value, $precision) }}/{{ $max }}
        </span>
    @endif
</div>

@unless($readonly)
@pushOnce('rating-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI Rating
    if (window.HSRating) {
        window.HSRating.autoInit();
    }
    
    // Custom rating functionality
    document.querySelectorAll('[data-hs-rating]').forEach(function(ratingContainer) {
        const stars = ratingContainer.querySelectorAll('.hs-rating-star');
        const hiddenInput = ratingContainer.parentNode.querySelector('input[type="hidden"]');
        
        stars.forEach(function(star, index) {
            star.addEventListener('click', function() {
                const value = parseInt(this.getAttribute('data-hs-rating-value'));
                
                // Update visual state
                stars.forEach(function(s, i) {
                    if (i < value) {
                        s.classList.add('{{ $currentVariant["filled"] }}');
                        s.classList.remove('{{ $currentVariant["empty"] }}');
                    } else {
                        s.classList.add('{{ $currentVariant["empty"] }}');
                        s.classList.remove('{{ $currentVariant["filled"] }}');
                    }
                });
                
                // Update hidden input
                if (hiddenInput) {
                    hiddenInput.value = value;
                }
                
                // Trigger change event
                ratingContainer.dispatchEvent(new CustomEvent('rating-change', {
                    detail: { value: value },
                    bubbles: true
                }));
            });
            
            // Hover effects
            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.getAttribute('data-hs-rating-value'));
                stars.forEach(function(s, i) {
                    if (i < value) {
                        s.style.opacity = '1';
                    } else {
                        s.style.opacity = '0.3';
                    }
                });
            });
        });
        
        ratingContainer.addEventListener('mouseleave', function() {
            stars.forEach(function(s) {
                s.style.opacity = '1';
            });
        });
    });
});
</script>
@endPushOnce
@endunless