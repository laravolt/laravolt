@php
    $variant = $attributes->get('variant', 'info');
    $position = $attributes->get('position', 'bottom-right');
    $autoHide = $attributes->get('auto-hide', true);
    $delay = $attributes->get('delay', 5000);
    $show = $attributes->get('show', true);
    $attributes = $attributes->except(['variant', 'position', 'auto-hide', 'delay', 'show']);

    $toastId = 'toast-' . uniqid();

    // Position classes
    $positionClasses = [
        'top-left' => 'top-4 left-4',
        'top-right' => 'top-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'top-center' => 'top-4 left-1/2 -translate-x-1/2',
        'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2'
    ];

    // Variant styles
    $variantClasses = [
        'success' => [
            'bg' => 'bg-green-50 border-green-200 dark:bg-green-900 dark:border-green-800',
            'icon' => 'text-green-500 dark:text-green-400',
            'text' => 'text-green-800 dark:text-green-200',
            'iconPath' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ],
        'error' => [
            'bg' => 'bg-red-50 border-red-200 dark:bg-red-900 dark:border-red-800',
            'icon' => 'text-red-500 dark:text-red-400',
            'text' => 'text-red-800 dark:text-red-200',
            'iconPath' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900 dark:border-yellow-800',
            'icon' => 'text-yellow-500 dark:text-yellow-400',
            'text' => 'text-yellow-800 dark:text-yellow-200',
            'iconPath' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />'
        ],
        'info' => [
            'bg' => 'bg-blue-50 border-blue-200 dark:bg-blue-900 dark:border-blue-800',
            'icon' => 'text-blue-500 dark:text-blue-400',
            'text' => 'text-blue-800 dark:text-blue-200',
            'iconPath' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ]
    ];

    $styles = $variantClasses[$variant] ?? $variantClasses['info'];
@endphp

<!-- Toast Container -->
<div
    id="{{ $toastId }}"
    class="fixed {{ $positionClasses[$position] ?? $positionClasses['bottom-right'] }} z-50 transition-all duration-300 {{ $show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2' }}"
    x-data="{
        show: {{ $show ? 'true' : 'false' }},
        autoHide: {{ $autoHide ? 'true' : 'false' }},
        delay: {{ $delay }},
        init() {
            if (this.show && this.autoHide) {
                setTimeout(() => {
                    this.hide();
                }, this.delay);
            }
        },
        hide() {
            this.show = false;
            setTimeout(() => {
                document.getElementById('{{ $toastId }}').remove();
            }, 300);
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
>
    <div class="max-w-sm w-full {{ $styles['bg'] }} border rounded-lg shadow-lg dark:shadow-neutral-900/20">
        <div class="flex items-start gap-3 p-4">
            <!-- Icon -->
            @if($variant !== 'custom')
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 {{ $styles['icon'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $styles['iconPath'] !!}
                    </svg>
                </div>
            @endif

            <!-- Content -->
            <div class="flex-1 min-w-0">
                @if($title ?? false)
                    <h4 class="text-sm font-semibold {{ $styles['text'] }}">
                        {{ $title }}
                    </h4>
                @endif
                <p class="text-sm {{ $styles['text'] }} opacity-90">
                    {{ $message ?? $slot }}
                </p>
            </div>

            <!-- Close Button -->
            <button
                type="button"
                class="flex-shrink-0 ml-auto -mx-1.5 -my-1.5 {{ $styles['text'] }} opacity-60 hover:opacity-100 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-black hover:bg-opacity-5 focus:outline-hidden focus:ring-2 focus:ring-gray-300 transition-colors duration-200"
                @click="hide()"
            >
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
