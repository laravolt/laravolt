@php
    $title = $attributes->get('title', '');
    $message = $attributes->get('message', '');
    $variant = $attributes->get('variant', 'info');
    $position = $attributes->get('position', 'top-right');
    $dismissible = $attributes->get('dismissible', true);
    $autoHide = $attributes->get('auto-hide', false);
    $duration = $attributes->get('duration', 5000);
    $icon = $attributes->get('icon', '');
    $actions = $attributes->get('actions', []);
    $id = $attributes->get('id', 'notification-' . uniqid());
    $attributes = $attributes->except(['title', 'message', 'variant', 'position', 'dismissible', 'auto-hide', 'duration', 'icon', 'actions', 'id']);

    // Position classes
    $positionClasses = [
        'top-left' => 'top-4 left-4',
        'top-center' => 'top-4 left-1/2 transform -translate-x-1/2',
        'top-right' => 'top-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'bottom-center' => 'bottom-4 left-1/2 transform -translate-x-1/2',
        'bottom-right' => 'bottom-4 right-4'
    ];

    // Variant styles
    $variantClasses = [
        'success' => [
            'container' => 'bg-teal-50 border-teal-200 text-teal-800 dark:bg-teal-800/10 dark:border-teal-900 dark:text-teal-500',
            'icon' => 'text-teal-600 dark:text-teal-500'
        ],
        'error' => [
            'container' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500',
            'icon' => 'text-red-600 dark:text-red-500'
        ],
        'warning' => [
            'container' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500',
            'icon' => 'text-yellow-600 dark:text-yellow-500'
        ],
        'info' => [
            'container' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-800/10 dark:border-blue-900 dark:text-blue-500',
            'icon' => 'text-blue-600 dark:text-blue-500'
        ]
    ];

    // Default icons
    $defaultIcons = [
        'success' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>',
        'error' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>',
        'warning' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>',
        'info' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>'
    ];

    $currentPosition = $positionClasses[$position] ?? $positionClasses['top-right'];
    $currentVariant = $variantClasses[$variant] ?? $variantClasses['info'];
    $currentIcon = $icon ?: $defaultIcons[$variant];
@endphp

<!-- Preline UI v3.0 Notification Component -->
<div 
    id="{{ $id }}"
    class="hs-removing:translate-x-5 hs-removing:opacity-0 fixed z-[60] {{ $currentPosition }} max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:bg-neutral-800 dark:border-neutral-700"
    role="alert"
    tabindex="-1"
    aria-labelledby="{{ $id }}-label"
    @if($autoHide) 
        data-hs-remove-element-options='{"timeout": {{ $duration }}}'
    @endif
    {{ $attributes }}
>
    <div class="flex p-4">
        @if($currentIcon)
            <!-- Icon -->
            <div class="shrink-0">
                <div class="{{ $currentVariant['icon'] }}">
                    {!! $currentIcon !!}
                </div>
            </div>
        @endif
        
        <!-- Content -->
        <div class="ms-3">
            @if($title)
                <p id="{{ $id }}-label" class="text-gray-800 font-medium dark:text-white">
                    {{ $title }}
                </p>
            @endif
            
            <div class="text-sm text-gray-600 dark:text-neutral-400">
                {{ $message ?? $slot }}
            </div>
            
            @if(!empty($actions))
                <!-- Actions -->
                <div class="mt-3 flex gap-2">
                    @foreach($actions as $action)
                        @if($action['type'] === 'button')
                            <button 
                                type="button"
                                class="inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400 dark:focus:text-blue-400"
                                @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                            >
                                {{ $action['label'] }}
                            </button>
                        @elseif($action['type'] === 'link')
                            <a 
                                href="{{ $action['href'] ?? '#' }}"
                                class="inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400 dark:focus:text-blue-400"
                            >
                                {{ $action['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        
        @if($dismissible)
            <!-- Dismiss Button -->
            <div class="ms-auto">
                <button 
                    type="button"
                    class="inline-flex shrink-0 justify-center items-center size-5 rounded-lg text-gray-800 opacity-50 hover:opacity-100 focus:outline-none focus:opacity-100 dark:text-white"
                    aria-label="Close"
                    data-hs-remove-element="#{{ $id }}"
                >
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>

@pushOnce('notification-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline UI Remove Element
    if (window.HSRemoveElement) {
        window.HSRemoveElement.autoInit();
    }
});

// Notification API for programmatic usage
window.PrelineNotification = {
    show: function(options) {
        const {
            title = '',
            message = '',
            variant = 'info',
            position = 'top-right',
            duration = 5000,
            dismissible = true,
            autoHide = true
        } = options;
        
        const notification = document.createElement('div');
        const id = 'notification-' + Date.now();
        
        // Create notification HTML (simplified version)
        notification.innerHTML = `
            <div id="${id}" class="fixed z-[60] ${position} max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                <div class="flex p-4">
                    <div class="ms-3">
                        ${title ? `<p class="text-gray-800 font-medium dark:text-white">${title}</p>` : ''}
                        <div class="text-sm text-gray-600 dark:text-neutral-400">${message}</div>
                    </div>
                    ${dismissible ? `
                        <div class="ms-auto">
                            <button type="button" class="inline-flex shrink-0 justify-center items-center size-5 rounded-lg text-gray-800 opacity-50 hover:opacity-100 focus:outline-none focus:opacity-100 dark:text-white" data-hs-remove-element="#${id}">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m18 6-12 12"></path>
                                    <path d="m6 6 12 12"></path>
                                </svg>
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        document.body.appendChild(notification.firstElementChild);
        
        if (autoHide) {
            setTimeout(() => {
                const el = document.getElementById(id);
                if (el) el.remove();
            }, duration);
        }
        
        return id;
    }
};
</script>
@endPushOnce