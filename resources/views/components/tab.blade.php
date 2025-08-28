@php
    $variant = $attributes->get('variant', 'default');
    $attributes = $attributes->except(['variant']);

    $containerClasses = 'bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700';
    $tabListClasses = 'flex flex-wrap border-b border-gray-200 dark:border-neutral-700';
    $tabContentClasses = 'p-6';
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    <!-- Tab Navigation -->
    <nav class="{{ $tabListClasses }}" aria-label="Tabs">
        <div class="flex space-x-8">
            @stack("tab.titles.$key")
        </div>
    </nav>

    <!-- Tab Content -->
    <div class="{{ $tabContentClasses }}">
        @stack("tab.contents.$key")
    </div>
</div>

@once
    @push('script')
        <script>
            // Initialize Preline UI tabs
            document.addEventListener('DOMContentLoaded', function() {
                // Preline UI tabs initialization will be handled by the framework
                console.log('Tabs initialized');
            });
        </script>
    @endpush
@endonce
