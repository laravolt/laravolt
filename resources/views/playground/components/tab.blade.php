{{-- Tabs — Preline hs-tab --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Tabs</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Tabbed interfaces powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-tab</code> with multiple styles.</p>

<div class="grid sm:grid-cols-2 gap-6">
    {{-- Underline Tabs --}}
    <x-volt-panel title="Underline Tabs">
        <div class="border-b border-gray-200 dark:border-neutral-700">
            <nav class="flex gap-x-1" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
                <button type="button" class="hs-tab-active:font-semibold hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-blue-500 active" id="underline-tab-1" aria-selected="true" data-hs-tab="#underline-content-1" aria-controls="underline-content-1" role="tab">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Overview
                </button>
                <button type="button" class="hs-tab-active:font-semibold hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-blue-500" id="underline-tab-2" aria-selected="false" data-hs-tab="#underline-content-2" aria-controls="underline-content-2" role="tab">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Settings
                </button>
                <button type="button" class="hs-tab-active:font-semibold hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-blue-500" id="underline-tab-3" aria-selected="false" data-hs-tab="#underline-content-3" aria-controls="underline-content-3" role="tab">
                    Notifications
                    <span class="ms-1 py-0.5 px-1.5 rounded-full text-xs font-medium bg-blue-50 border border-blue-200 text-blue-600 dark:bg-blue-800/30 dark:border-blue-900 dark:text-blue-400">3</span>
                </button>
            </nav>
        </div>
        <div class="mt-3">
            <div id="underline-content-1" role="tabpanel" aria-labelledby="underline-tab-1">
                <p class="text-gray-500 dark:text-neutral-400 text-sm">Welcome to the overview panel. This tab uses Preline's <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1 py-0.5 rounded">hs-tab-active:</code> variant for active state styling.</p>
            </div>
            <div id="underline-content-2" class="hidden" role="tabpanel" aria-labelledby="underline-tab-2">
                <p class="text-gray-500 dark:text-neutral-400 text-sm">Settings panel with icons. All tabs follow WAI-ARIA tablist pattern for full accessibility.</p>
            </div>
            <div id="underline-content-3" class="hidden" role="tabpanel" aria-labelledby="underline-tab-3">
                <p class="text-gray-500 dark:text-neutral-400 text-sm">You have 3 unread notifications. Tabs support badges and icons inline.</p>
            </div>
        </div>
    </x-volt-panel>

    {{-- Pill (Segment) Tabs --}}
    <x-volt-panel title="Segment / Pill Tabs">
        <nav class="flex gap-x-1 bg-gray-100 rounded-lg p-1 dark:bg-neutral-700" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
            <button type="button" class="hs-tab-active:bg-white hs-tab-active:text-gray-700 hs-tab-active:dark:bg-neutral-800 hs-tab-active:dark:text-neutral-400 py-2.5 px-4 inline-flex items-center gap-x-2 bg-transparent text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 font-medium rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-white dark:focus:text-white active" id="segment-tab-1" aria-selected="true" data-hs-tab="#segment-content-1" aria-controls="segment-content-1" role="tab">
                Daily
            </button>
            <button type="button" class="hs-tab-active:bg-white hs-tab-active:text-gray-700 hs-tab-active:dark:bg-neutral-800 hs-tab-active:dark:text-neutral-400 py-2.5 px-4 inline-flex items-center gap-x-2 bg-transparent text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 font-medium rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-white dark:focus:text-white" id="segment-tab-2" aria-selected="false" data-hs-tab="#segment-content-2" aria-controls="segment-content-2" role="tab">
                Weekly
            </button>
            <button type="button" class="hs-tab-active:bg-white hs-tab-active:text-gray-700 hs-tab-active:dark:bg-neutral-800 hs-tab-active:dark:text-neutral-400 py-2.5 px-4 inline-flex items-center gap-x-2 bg-transparent text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 font-medium rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-white dark:focus:text-white" id="segment-tab-3" aria-selected="false" data-hs-tab="#segment-content-3" aria-controls="segment-content-3" role="tab">
                Monthly
            </button>
        </nav>
        <div class="mt-3">
            <div id="segment-content-1" role="tabpanel" aria-labelledby="segment-tab-1">
                <p class="text-gray-500 dark:text-neutral-400 text-sm">Showing daily metrics. Segment tabs use <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1 py-0.5 rounded">hs-tab-active:bg-white</code> for the pill effect.</p>
            </div>
            <div id="segment-content-2" class="hidden" role="tabpanel" aria-labelledby="segment-tab-2">
                <p class="text-gray-500 dark:text-neutral-400 text-sm">Weekly data aggregation view. Great for time-range selectors.</p>
            </div>
            <div id="segment-content-3" class="hidden" role="tabpanel" aria-labelledby="segment-tab-3">
                <p class="text-gray-500 dark:text-neutral-400 text-sm">Monthly breakdown. These pill tabs feel like iOS segmented controls.</p>
            </div>
        </div>
    </x-volt-panel>
</div>
