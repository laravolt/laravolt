<div class="border border-gray-200 rounded-xl bg-white dark:bg-neutral-800 dark:border-neutral-700" data-role="quick-menu">
    <div class="p-3 border-b border-gray-200 dark:border-neutral-700">
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="shrink-0 size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <input type="text"
                   class="py-2 ps-10 pe-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                   placeholder=""
                   data-role="quick-menu-searchbox">
        </div>
    </div>
    <div class="items hs-accordion-group" data-hs-accordion-always-open></div>
</div>

<style>
    [data-role="quick-menu"] .items:empty {
        margin: 0;
    }
</style>
