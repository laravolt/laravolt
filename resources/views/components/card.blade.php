@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'footer' => null,
    'padding' => true,
    'border' => true,
    'shadow' => true
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden' . 
    ($border ? ' border border-gray-200 dark:border-gray-700' : '') .
    ($shadow ? ' shadow-sm' : '') .
    ' rounded-xl dark:bg-slate-900'
]) }}>
    
    @if($title || $subtitle || $actions)
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-4 sm:px-6">
        <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
                @if($title)
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
                @endif
                @if($subtitle)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $subtitle }}
                </p>
                @endif
            </div>
            @if($actions)
            <div class="flex items-center space-x-2">
                {{ $actions }}
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Body -->
    <div class="{{ $padding ? 'px-4 py-4 sm:px-6' : '' }}">
        {{ $slot }}
    </div>
    
    @if($footer)
    <!-- Footer -->
    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6 bg-gray-50 dark:bg-slate-800">
        {{ $footer }}
    </div>
    @endif
    
</div>

