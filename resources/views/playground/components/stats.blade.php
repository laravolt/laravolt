{{-- Stats Cards — Dashboard-style stat widgets --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Stats Cards</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Dashboard-style stat widgets with gradient accents and trend indicators.</p>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    {{-- Revenue --}}
    <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">Total Revenue</p>
            <div class="hs-tooltip">
                <div class="hs-tooltip-toggle">
                    <svg class="shrink-0 size-4 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                    <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-neutral-700" role="tooltip">Net revenue after refunds</span>
                </div>
            </div>
        </div>
        <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">Rp 72.8 M</h3>
            <span class="flex items-center gap-x-1 text-green-600"><svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg><span class="inline-block text-sm">12.5%</span></span>
        </div>
    </div>

    {{-- Users --}}
    <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">Active Users</p>
        </div>
        <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">3,241</h3>
            <span class="flex items-center gap-x-1 text-green-600"><svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg><span class="inline-block text-sm">4.6%</span></span>
        </div>
    </div>

    {{-- Sessions --}}
    <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">Avg. Session</p>
        </div>
        <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">4m 32s</h3>
            <span class="flex items-center gap-x-1 text-red-600"><svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg><span class="inline-block text-sm">1.7%</span></span>
        </div>
    </div>

    {{-- Completion --}}
    <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">Success Rate</p>
        </div>
        <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">92.6%</h3>
        </div>
        <div class="mt-3">
            <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700">
                <div class="flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition-all duration-500 dark:bg-blue-500" role="progressbar" style="width: 92.6%" aria-valuenow="92.6" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</div>
