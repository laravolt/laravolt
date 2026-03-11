{{-- Tooltip & Popover — Preline hs-tooltip --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Tooltip & Popover</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Contextual info on hover powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-tooltip</code>.</p>

<x-volt-panel title="Tooltip Positions">
    <div class="flex flex-wrap items-center gap-4">
        {{-- Top --}}
        <div class="hs-tooltip inline-block [--placement:top]">
            <button type="button" class="hs-tooltip-toggle py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                Top
                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1.5 px-2.5 bg-gray-900 text-xs font-medium text-white rounded-lg shadow-sm dark:bg-neutral-700" role="tooltip">Tooltip on top</span>
            </button>
        </div>

        {{-- Bottom --}}
        <div class="hs-tooltip inline-block [--placement:bottom]">
            <button type="button" class="hs-tooltip-toggle py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                Bottom
                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1.5 px-2.5 bg-gray-900 text-xs font-medium text-white rounded-lg shadow-sm dark:bg-neutral-700" role="tooltip">Tooltip on bottom</span>
            </button>
        </div>

        {{-- Left --}}
        <div class="hs-tooltip inline-block [--placement:left]">
            <button type="button" class="hs-tooltip-toggle py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                Left
                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1.5 px-2.5 bg-gray-900 text-xs font-medium text-white rounded-lg shadow-sm dark:bg-neutral-700" role="tooltip">Tooltip on left</span>
            </button>
        </div>

        {{-- Right --}}
        <div class="hs-tooltip inline-block [--placement:right]">
            <button type="button" class="hs-tooltip-toggle py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                Right
                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1.5 px-2.5 bg-gray-900 text-xs font-medium text-white rounded-lg shadow-sm dark:bg-neutral-700" role="tooltip">Tooltip on right</span>
            </button>
        </div>

        {{-- Blue with Icon --}}
        <div class="hs-tooltip inline-block [--placement:top]">
            <button type="button" class="hs-tooltip-toggle py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                Help
                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-2 px-3 bg-gray-900 text-sm text-white rounded-lg shadow-sm dark:bg-neutral-700" role="tooltip">
                    Click for help documentation
                </span>
            </button>
        </div>
    </div>
</x-volt-panel>
