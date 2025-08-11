@php
    /** @var \Laravolt\Platform\Services\SidebarMenu */
    $items = app('laravolt.menu.sidebar')->allMenu();
@endphp

<aside id="hs-pro-sidebar"
    class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform w-65 h-full hidden fixed inset-y-0 start-0 z-60 bg-white border-e border-gray-200 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 dark:bg-neutral-800 dark:border-neutral-700"
    tabindex="-1" aria-label="Sidebar">
    <div class="relative flex flex-col h-full max-h-full pt-3">
        <header class="h-11.5 ps-5 pe-2 lg:ps-8 flex items-center gap-x-1">
            <!-- Logo -->
            <a class="flex-none rounded-md text-xl inline-block font-semibold focus:outline-hidden focus:opacity-80"
                href="#" aria-label="Preline">
                <x-volt-brand-image />
            </a>
            <!-- End Logo -->

            <div class="lg:hidden ms-auto">
                <!-- Sidebar Close -->
                <button type="button"
                    class="w-6 h-7 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                    data-hs-overlay="#hs-pro-sidebar" aria-expanded="false">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="7 8 3 12 7 16"></polyline>
                        <line x1="21" x2="11" y1="12" y2="12"></line>
                        <line x1="21" x2="11" y1="6" y2="6"></line>
                        <line x1="21" x2="11" y1="18" y2="18"></line>
                    </svg>
                </button>
                <!-- End Sidebar Close -->
            </div>
        </header>

        <!-- Content -->
        <div
            class="mt-1.5 h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <!-- Nav -->
            <nav class="hs-accordion-group pb-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="flex flex-col gap-y-1">
                    @include('laravolt::menu.sidebar_menu')
                </ul>
            </nav>
            <!-- End Nav -->
        </div>
        <!-- End Content -->

        <footer class="hidden lg:block border-t border-gray-200 dark:border-neutral-700">
            <!-- Project Dropdown -->

            <!-- End Project Dropdown -->
        </footer>
    </div>
</aside>
