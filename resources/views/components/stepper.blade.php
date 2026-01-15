@php
    $steps = $attributes->get('steps', []);
    $currentStep = $attributes->get('current-step', 1);
    $orientation = $attributes->get('orientation', 'horizontal');
    $variant = $attributes->get('variant', 'default');
    $attributes = $attributes->except(['steps', 'current-step', 'orientation', 'variant']);

    $stepperId = 'stepper-' . uniqid();

    // Variant styles
    $variantClasses = [
        'default' => [
            'container' => 'flex items-center',
            'step' => 'flex items-center justify-center w-8 h-8 rounded-full border-2 text-sm font-medium transition-colors duration-200',
            'active' => 'bg-blue-600 border-blue-600 text-white',
            'completed' => 'bg-green-600 border-green-600 text-white',
            'pending' => 'border-gray-300 text-gray-500 bg-white dark:border-neutral-600 dark:text-neutral-400 dark:bg-neutral-800',
            'line' => 'flex-auto h-0.5 bg-gray-300 dark:bg-neutral-600',
            'completed-line' => 'bg-green-600',
            'label' => 'text-sm font-medium',
            'description' => 'text-xs text-gray-500 dark:text-neutral-400'
        ],
        'numbered' => [
            'container' => 'flex items-center',
            'step' => 'flex items-center justify-center w-10 h-10 rounded-full border-2 text-sm font-semibold transition-colors duration-200',
            'active' => 'bg-blue-600 border-blue-600 text-white',
            'completed' => 'bg-green-600 border-green-600 text-white',
            'pending' => 'border-gray-300 text-gray-500 bg-white dark:border-neutral-600 dark:text-neutral-400 dark:bg-neutral-800',
            'line' => 'flex-auto h-0.5 bg-gray-300 dark:bg-neutral-600',
            'completed-line' => 'bg-green-600',
            'label' => 'text-sm font-semibold',
            'description' => 'text-xs text-gray-500 dark:text-neutral-400'
        ]
    ];

    $styles = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

@if($orientation === 'vertical')
    <div class="space-y-6">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isActive = $stepNumber === $currentStep;
                $isCompleted = $stepNumber < $currentStep;
                $isPending = $stepNumber > $currentStep;
            @endphp

            <div class="flex items-start gap-x-4">
                <!-- Step Circle -->
                <div class="flex-shrink-0">
                    <div class="{{ $styles['step'] }} {{
                        $isActive ? $styles['active'] :
                        ($isCompleted ? $styles['completed'] : $styles['pending'])
                    }}">
                        @if($isCompleted)
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>
                </div>

                <!-- Step Content -->
                <div class="min-w-0 flex-1">
                    <h3 class="{{ $styles['label'] }} {{
                        $isActive ? 'text-blue-600 dark:text-blue-400' :
                        ($isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-neutral-200')
                    }}">
                        {{ $step['title'] }}
                    </h3>
                    @if(isset($step['description']))
                        <p class="{{ $styles['description'] }} mt-1">
                            {{ $step['description'] }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Horizontal Stepper -->
    <div class="{{ $styles['container'] }} gap-x-4">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isActive = $stepNumber === $currentStep;
                $isCompleted = $stepNumber < $currentStep;
                $isPending = $stepNumber > $currentStep;
                $isLast = $index === count($steps) - 1;
            @endphp

            <!-- Step -->
            <div class="flex items-center gap-x-2">
                <div class="flex-shrink-0">
                    <div class="{{ $styles['step'] }} {{
                        $isActive ? $styles['active'] :
                        ($isCompleted ? $styles['completed'] : $styles['pending'])
                    }}">
                        @if($isCompleted)
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>
                </div>

                <!-- Step Label -->
                <div class="hidden sm:block">
                    <div class="{{ $styles['label'] }} {{
                        $isActive ? 'text-blue-600 dark:text-blue-400' :
                        ($isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-neutral-200')
                    }}">
                        {{ $step['title'] }}
                    </div>
                    @if(isset($step['description']))
                        <div class="{{ $styles['description'] }}">
                            {{ $step['description'] }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Connector Line -->
            @if(!$isLast)
                <div class="{{ $styles['line'] }} {{ $isCompleted ? $styles['completed-line'] : '' }} mx-2"></div>
            @endif
        @endforeach
    </div>
@endif
