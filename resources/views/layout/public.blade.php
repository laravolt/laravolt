@php
    $title = $title ?? 'Preline UI Showcase';
@endphp

<x-volt-base :title="$title">
    {{-- Minimal topbar for public showcase --}}
    <header class="sticky top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap w-full bg-white border-b text-sm py-2.5 lg:py-4 dark:bg-neutral-800 dark:border-neutral-700" style="z-index:48">
        <nav class="px-4 sm:px-6 flex basis-full items-center w-full mx-auto">
            <div class="me-5 lg:me-0">
                <a class="flex-none rounded-md text-xl inline-block font-semibold focus:outline-none focus:opacity-80 text-blue-600 dark:text-blue-500 whitespace-nowrap" href="/" aria-label="Preline UI">Preline UI</a>
            </div>
            <div class="w-full flex items-center justify-end ms-auto gap-x-3">
                <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-blue-50 text-blue-600 dark:bg-blue-800/30 dark:text-blue-400">v4.1.2</span>
                {{-- Theme Toggle --}}
                <button type="button" class="hs-dark-mode group flex items-center text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600 font-medium dark:text-neutral-400 dark:hover:text-neutral-500 dark:focus:text-neutral-500" data-hs-theme-click-value="dark">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>
                <button type="button" class="hs-dark-mode group flex items-center text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600 font-medium dark:text-neutral-400 dark:hover:text-neutral-500 dark:focus:text-neutral-500" data-hs-theme-click-value="light">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                </button>
                <a href="https://preline.co/docs" target="_blank" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                    Docs
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                </a>
            </div>
        </nav>
    </header>

    <main id="content" class="pb-10 sm:pb-16">
        <div class="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto pt-6 space-y-5">
            @if (isset($title))
                <div class="flex justify-between items-center gap-x-5">
                    <h2 class="inline-block text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $title }}
                    </h2>
                </div>
            @endif

            {{ $slot ?? null }}
        </div>
    </main>

    <footer class="h-10 sm:h-16">
        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-4">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-neutral-200">
                &copy; {{ now()->year }} {{ config('app.name') }}. Powered by <a href="https://preline.co" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">Preline UI</a>.
            </p>
        </div>
    </footer>
</x-volt-base>
