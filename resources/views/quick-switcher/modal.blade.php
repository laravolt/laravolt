<!-- Quick Switcher Modal -->
<div id="hs-quick-switcher-modal"
     class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto"
     role="dialog" tabindex="-1"
     data-role="quick-switcher-modal">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
            <div class="p-4">
                <select class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                        data-role="quick-switcher-dropdown">
                    <option value="">@lang('Type to find an action')</option>
                </select>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
    </script>
@endpush
