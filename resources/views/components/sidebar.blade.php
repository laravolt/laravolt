@php
    $variant = $attributes->get('variant', 'default');
    $collapsible = $attributes->get('collapsible', false);
    $attributes = $attributes->except(['variant', 'collapsible']);

    // Variant styles
    $variantClasses = [
        'default' => 'space-y-8 pt-5 pb-10 sm:pt-7 px-4 sm:px-8 -my-8 lg:my-0',
        'compact' => 'space-y-6 pt-4 pb-8 px-3 -my-6',
        'minimal' => 'space-y-4 pt-3 pb-6 px-2 -my-4'
    ];

    $baseClasses = 'relative space-y-8 pt-5 pb-10 sm:pt-7 px-4 sm:px-8 -my-8 lg:my-0';
    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['default']);
@endphp

<nav {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</nav>

{{-- Example usage with sections --}}
@unless($slot)
    <!-- Main Navigation -->
    <ul class="space-y-3">
        <li>
            <a
                class="group flex items-center gap-x-2 text-sm font-semibold text-gray-700 hover:text-blue-600 focus:outline-hidden focus:text-blue-600 dark:text-neutral-400 dark:hover:text-neutral-300 dark:focus:text-neutral-300 font-semibold text-blue-600! dark:text-blue-500!"
                href="#"
            >
                <div class="p-1.5 border border-gray-200 rounded-lg shadow-2xs group-hover:shadow-xs dark:border-neutral-800 dark:group-hover:border-neutral-700">
                    <svg class="shrink-0 size-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                </div>
                Documentation
            </a>
        </li>

        <li>
            <a
                class="group flex items-center gap-x-2 text-sm font-semibold text-gray-700 hover:text-blue-600 focus:outline-hidden focus:text-blue-600 dark:text-neutral-400 dark:hover:text-neutral-300 dark:focus:text-neutral-300"
                href="#"
            >
                <div class="p-1.5 border border-gray-200 rounded-lg shadow-2xs group-hover:shadow-xs dark:border-neutral-800 dark:group-hover:border-neutral-700">
                    <svg class="shrink-0 size-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                </div>
                Components
                <x-volt-badge variant="success" class="ml-auto">New</x-volt-badge>
            </a>
        </li>
    </ul>

    <!-- Section with Border -->
    <ul>
        <li>
            <h5 class="mb-3 text-sm font-semibold text-gray-800 dark:text-neutral-200">
                Base Components
            </h5>
            <ul class="ms-0.5 space-y-2 border-s-2 border-gray-100 dark:border-neutral-800">
                <li>
                    <a
                        class="block py-1 ps-4 -ms-px border-s-2 border-transparent text-sm text-gray-700 hover:border-gray-400 hover:text-neutral-900 focus:outline-hidden focus:border-gray-400 focus:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-300 dark:focus:text-neutral-300"
                        href="#"
                    >
                        Buttons
                    </a>
                </li>
                <li>
                    <a
                        class="block py-1 ps-4 -ms-px border-s-2 border-transparent text-sm text-gray-700 hover:border-gray-400 hover:text-neutral-900 focus:outline-hidden focus:border-gray-400 focus:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-300 dark:focus:text-neutral-300"
                        href="#"
                    >
                        Cards
                    </a>
                </li>
                <li>
                    <a
                        class="block py-1 ps-4 -ms-px border-s-2 border-blue-600 text-sm text-blue-600 font-semibold border-s-2 border-blue-600 hover:border-blue-600 focus:outline-hidden focus:border-blue-600 dark:text-blue-400 dark:border-blue-500"
                        href="#"
                    >
                        Forms
                    </a>
                </li>
            </ul>
        </li>
    </ul>
@endunless
