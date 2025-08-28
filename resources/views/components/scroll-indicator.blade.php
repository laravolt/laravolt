@php
    $variant = $attributes->get('variant', 'top');
    $size = $attributes->get('size', 'md');
    $color = $attributes->get('color', 'blue');
    $position = $attributes->get('position', 'fixed');
    $target = $attributes->get('target', 'body');
    $id = $attributes->get('id', 'scroll-indicator-' . uniqid());
    $attributes = $attributes->except(['variant', 'size', 'color', 'position', 'target', 'id']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'xs' => 'h-0.5',
        'sm' => 'h-1',
        'md' => 'h-1.5',
        'lg' => 'h-2',
        'xl' => 'h-3'
    ];

    // Color variants
    $colorClasses = [
        'blue' => 'bg-blue-600',
        'red' => 'bg-red-600',
        'green' => 'bg-green-600',
        'teal' => 'bg-teal-600',
        'yellow' => 'bg-yellow-500',
        'purple' => 'bg-purple-600',
        'gray' => 'bg-gray-600'
    ];

    // Position variants
    $positionClasses = [
        'top' => 'top-0 left-0 right-0',
        'bottom' => 'bottom-0 left-0 right-0'
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentColor = $colorClasses[$color] ?? $colorClasses['blue'];
    $currentPosition = $positionClasses[$variant] ?? $positionClasses['top'];
@endphp

<!-- Preline UI v3.0 Scroll Indicator Component -->
<div 
    id="{{ $id }}"
    class="{{ $position }} {{ $currentPosition }} z-50 {{ $currentSize }} bg-gray-200 dark:bg-neutral-700"
    {{ $attributes }}
>
    <div 
        class="h-full {{ $currentColor }} transition-all duration-150 ease-out"
        style="width: 0%"
        data-scroll-indicator-progress
    ></div>
</div>

@pushOnce('scroll-indicator-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize scroll indicators
    document.querySelectorAll('[data-scroll-indicator-progress]').forEach(function(progressBar) {
        const container = progressBar.parentElement;
        const target = container.getAttribute('data-target') || 'body';
        const targetElement = target === 'body' ? document.body : document.querySelector(target);
        
        if (!targetElement) return;
        
        function updateProgress() {
            let scrollTop, scrollHeight, clientHeight;
            
            if (target === 'body') {
                scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                scrollHeight = document.documentElement.scrollHeight;
                clientHeight = window.innerHeight;
            } else {
                scrollTop = targetElement.scrollTop;
                scrollHeight = targetElement.scrollHeight;
                clientHeight = targetElement.clientHeight;
            }
            
            const scrollPercent = (scrollTop / (scrollHeight - clientHeight)) * 100;
            const clampedPercent = Math.min(Math.max(scrollPercent, 0), 100);
            
            progressBar.style.width = clampedPercent + '%';
            
            // Trigger custom event
            container.dispatchEvent(new CustomEvent('scroll-progress', {
                detail: { 
                    percent: clampedPercent,
                    scrollTop: scrollTop,
                    scrollHeight: scrollHeight
                },
                bubbles: true
            }));
        }
        
        // Listen to scroll events
        if (target === 'body') {
            window.addEventListener('scroll', updateProgress, { passive: true });
            window.addEventListener('resize', updateProgress, { passive: true });
        } else {
            targetElement.addEventListener('scroll', updateProgress, { passive: true });
        }
        
        // Initial update
        updateProgress();
    });
});
</script>
@endPushOnce