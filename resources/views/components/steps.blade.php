@php
    $steps = $attributes->get('steps', []);
    $currentStep = $attributes->get('current-step', 1);
    $orientation = $attributes->get('orientation', 'horizontal');
    $size = $attributes->get('size', 'md');
    $variant = $attributes->get('variant', 'default');
    $showNumbers = $attributes->get('show-numbers', true);
    $clickable = $attributes->get('clickable', false);
    $attributes = $attributes->except(['steps', 'current-step', 'orientation', 'size', 'variant', 'show-numbers', 'clickable']);

    // Size variants for Preline UI v3.0
    $sizeClasses = [
        'sm' => [
            'number' => 'size-6 text-xs',
            'text' => 'text-xs',
            'connector' => 'h-px'
        ],
        'md' => [
            'number' => 'size-8 text-sm',
            'text' => 'text-sm',
            'connector' => 'h-px'
        ],
        'lg' => [
            'number' => 'size-10 text-base',
            'text' => 'text-base',
            'connector' => 'h-0.5'
        ]
    ];

    // Variant styles
    $variantClasses = [
        'default' => [
            'completed' => 'bg-blue-600 text-white border-blue-600',
            'current' => 'bg-white text-blue-600 border-blue-600 dark:bg-neutral-800 dark:text-blue-400 dark:border-blue-400',
            'pending' => 'bg-gray-100 text-gray-500 border-gray-300 dark:bg-neutral-700 dark:text-neutral-400 dark:border-neutral-600',
            'connector_completed' => 'bg-blue-600',
            'connector_pending' => 'bg-gray-200 dark:bg-neutral-700'
        ],
        'success' => [
            'completed' => 'bg-teal-600 text-white border-teal-600',
            'current' => 'bg-white text-teal-600 border-teal-600 dark:bg-neutral-800 dark:text-teal-400 dark:border-teal-400',
            'pending' => 'bg-gray-100 text-gray-500 border-gray-300 dark:bg-neutral-700 dark:text-neutral-400 dark:border-neutral-600',
            'connector_completed' => 'bg-teal-600',
            'connector_pending' => 'bg-gray-200 dark:bg-neutral-700'
        ]
    ];

    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $currentVariant = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<!-- Preline UI v3.0 Steps Component -->
<div {{ $attributes->merge(['class' => 'flex items-center justify-between w-full']) }}>
    @if($slot->isNotEmpty())
        {{ $slot }}
    @else
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isCompleted = $stepNumber < $currentStep;
                $isCurrent = $stepNumber === $currentStep;
                $isPending = $stepNumber > $currentStep;
                $isLast = $index === count($steps) - 1;

                $title = $step['title'] ?? "Step {$stepNumber}";
                $description = $step['description'] ?? null;
                $icon = $step['icon'] ?? null;
                $href = $step['href'] ?? null;
            @endphp

            <div class="flex items-center {{ $isLast ? '' : 'flex-1' }}">
                <!-- Step Item -->
                <div class="flex flex-col items-center text-center">
                    @if($clickable && $href)
                        <a href="{{ $href }}" class="group">
                    @endif

                    <!-- Step Number/Icon -->
                    <div class="flex items-center justify-center {{ $currentSize['number'] }} border-2 rounded-full {{ $isCompleted ? $currentVariant['completed'] : ($isCurrent ? $currentVariant['current'] : $currentVariant['pending']) }} {{ $clickable ? 'hover:scale-110 transition-transform duration-200' : '' }}">
                        @if($isCompleted && !$icon)
                            <!-- Checkmark for completed steps -->
                            <svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                        @elseif($icon)
                            @if(str_contains($icon, '<svg'))
                                {!! $icon !!}
                            @else
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <use xlink:href="#{{ $icon }}"></use>
                                </svg>
                            @endif
                        @elseif($showNumbers)
                            {{ $stepNumber }}
                        @endif
                    </div>

                    <!-- Step Content -->
                    <div class="mt-2 {{ $currentSize['text'] }}">
                        <h3 class="font-medium {{ $isCompleted || $isCurrent ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-neutral-400' }}">
                            {{ $title }}
                        </h3>
                        @if($description)
                            <p class="text-gray-500 dark:text-neutral-400 mt-1">
                                {{ $description }}
                            </p>
                        @endif
                    </div>

                    @if($clickable && $href)
                        </a>
                    @endif
                </div>

                <!-- Connector Line -->
                @if(!$isLast)
                    <div class="flex-1 mx-4">
                        <div class="w-full {{ $currentSize['connector'] }} {{ $isCompleted ? $currentVariant['connector_completed'] : $currentVariant['connector_pending'] }}"></div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>

@if($clickable)
@pushOnce('steps-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handling for steps if needed
    document.querySelectorAll('[data-step-clickable]').forEach(function(step) {
        step.addEventListener('click', function() {
            const stepNumber = this.getAttribute('data-step-number');
            // Trigger custom event
            this.dispatchEvent(new CustomEvent('step-click', {
                detail: { step: parseInt(stepNumber) },
                bubbles: true
            }));
        });
    });
});
</script>
@endPushOnce
@endif