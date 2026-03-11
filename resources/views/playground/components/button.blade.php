{{-- Buttons — Preline UI showcase --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Buttons</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Button variants, sizes, and color options following Preline UI patterns.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Solid, Soft, Outline, Ghost & Link variants</p>
    </div>
    <div class="p-4 md:p-5 space-y-6">
        {{-- Solid Buttons --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Solid</p>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700">Blue</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-teal-600 text-white hover:bg-teal-700 focus:outline-none focus:bg-teal-700">Teal</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700">Red</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-yellow-500 text-white hover:bg-yellow-600 focus:outline-none focus:bg-yellow-600">Yellow</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:bg-indigo-700">Indigo</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 focus:outline-none focus:bg-purple-700">Purple</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-pink-600 text-white hover:bg-pink-700 focus:outline-none focus:bg-pink-700">Pink</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-800 text-white hover:bg-gray-900 focus:outline-none focus:bg-gray-900 dark:bg-white dark:text-neutral-800 dark:hover:bg-neutral-200">Dark</button>
            </div>
        </div>

        {{-- Soft Buttons --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Soft</p>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 focus:outline-none focus:bg-blue-200 dark:text-blue-400 dark:bg-blue-800/30 dark:hover:bg-blue-800/20">Blue</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-teal-100 text-teal-800 hover:bg-teal-200 focus:outline-none focus:bg-teal-200 dark:text-teal-400 dark:bg-teal-800/30 dark:hover:bg-teal-800/20">Teal</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-100 text-red-800 hover:bg-red-200 focus:outline-none focus:bg-red-200 dark:text-red-400 dark:bg-red-800/30 dark:hover:bg-red-800/20">Red</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-yellow-100 text-yellow-800 hover:bg-yellow-200 focus:outline-none focus:bg-yellow-200 dark:text-yellow-400 dark:bg-yellow-800/30 dark:hover:bg-yellow-800/20">Yellow</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-100 text-indigo-800 hover:bg-indigo-200 focus:outline-none focus:bg-indigo-200 dark:text-indigo-400 dark:bg-indigo-800/30 dark:hover:bg-indigo-800/20">Indigo</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-purple-100 text-purple-800 hover:bg-purple-200 focus:outline-none focus:bg-purple-200 dark:text-purple-400 dark:bg-purple-800/30 dark:hover:bg-purple-800/20">Purple</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-pink-100 text-pink-800 hover:bg-pink-200 focus:outline-none focus:bg-pink-200 dark:text-pink-400 dark:bg-pink-800/30 dark:hover:bg-pink-800/20">Pink</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-white dark:bg-neutral-700 dark:hover:bg-neutral-600">Dark</button>
            </div>
        </div>

        {{-- Outline Buttons --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Outline</p>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-blue-600 text-blue-600 hover:border-blue-500 hover:text-blue-500 focus:outline-none focus:border-blue-500 focus:text-blue-500 dark:border-blue-500 dark:text-blue-500 dark:hover:text-blue-400 dark:hover:border-blue-400">Blue</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-teal-600 text-teal-600 hover:border-teal-500 hover:text-teal-500 focus:outline-none focus:border-teal-500 focus:text-teal-500 dark:border-teal-500 dark:text-teal-500 dark:hover:text-teal-400 dark:hover:border-teal-400">Teal</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-red-600 text-red-600 hover:border-red-500 hover:text-red-500 focus:outline-none focus:border-red-500 focus:text-red-500 dark:border-red-500 dark:text-red-500 dark:hover:text-red-400 dark:hover:border-red-400">Red</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-yellow-500 text-yellow-500 hover:border-yellow-400 hover:text-yellow-400 focus:outline-none focus:border-yellow-400 focus:text-yellow-400">Yellow</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-indigo-600 text-indigo-600 hover:border-indigo-500 hover:text-indigo-500 focus:outline-none focus:border-indigo-500 focus:text-indigo-500 dark:border-indigo-500 dark:text-indigo-500 dark:hover:text-indigo-400 dark:hover:border-indigo-400">Indigo</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-800 text-gray-800 hover:border-gray-500 hover:text-gray-500 focus:outline-none dark:border-white dark:text-white dark:hover:text-neutral-300 dark:hover:border-neutral-300">Dark</button>
            </div>
        </div>

        {{-- Ghost & Link --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Ghost & Link</p>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent text-blue-600 hover:bg-blue-100 hover:text-blue-800 focus:outline-none focus:bg-blue-100 focus:text-blue-800 dark:text-blue-500 dark:hover:bg-blue-800/30 dark:hover:text-blue-400">Ghost Blue</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent text-teal-600 hover:bg-teal-100 hover:text-teal-800 focus:outline-none focus:bg-teal-100 focus:text-teal-800 dark:text-teal-500 dark:hover:bg-teal-800/30 dark:hover:text-teal-400">Ghost Teal</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent text-red-600 hover:bg-red-100 hover:text-red-800 focus:outline-none focus:bg-red-100 focus:text-red-800 dark:text-red-500 dark:hover:bg-red-800/30 dark:hover:text-red-400">Ghost Red</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline">Link Style</button>
            </div>
        </div>

        {{-- Sizes --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Sizes</p>
            <div class="flex flex-wrap items-end gap-2">
                <button type="button" class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">XS</button>
                <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">SM</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">MD</button>
                <button type="button" class="py-3 px-4 inline-flex items-center gap-x-2 text-base font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">LG</button>
                <button type="button" class="py-3.5 px-5 inline-flex items-center gap-x-2 text-base font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">XL</button>
            </div>
        </div>

        {{-- States --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">States</p>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-transparent bg-blue-600 text-white hover:bg-blue-700">Pill Button</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white" disabled>Disabled</button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 pointer-events-none">
                    <span class="inline-block rounded-full" role="status" aria-label="loading" style="width:1rem;height:1rem;border:3px solid currentColor;border-top-color:transparent;animation:spin 1s linear infinite"></span>
                    Loading
                </button>
                <button type="button" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    With Icon
                </button>
            </div>
        </div>
    </div>
</div>
