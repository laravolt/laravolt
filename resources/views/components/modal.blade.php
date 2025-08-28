@php
    $size = $attributes->get('size', 'md');
    $attributes = $attributes->except(['size']);

    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        'full' => 'max-w-full'
    ];
@endphp

<!-- Modal Backdrop -->
<div
    x-show="activeModal == '{{ $this->key }}'"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70"
    @click="close()"
    x-ref="{{ $this->key }}-backdrop"
></div>

<!-- Modal Dialog -->
<div
    x-show="activeModal == '{{ $this->key }}'"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 overflow-x-hidden overflow-y-auto"
    x-ref="{{ $this->key }}"
>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div
            {{ $attributes->merge(['class' => 'relative w-full ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' bg-white rounded-xl shadow-xl dark:bg-neutral-800']) }}
            @click.stop
        >
            <!-- Close button -->
            <button
                type="button"
                class="absolute top-3 right-3 inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors duration-200 dark:text-neutral-400 dark:hover:text-neutral-200 dark:hover:bg-neutral-700"
                @click="close()"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal content -->
            <div class="p-6 pt-12">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
