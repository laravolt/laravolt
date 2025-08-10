<x-volt-app title="Preline UI – Kitchen Sink">
    <div class="space-y-10">
        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Typography</h2>
            <div class="mt-3 space-y-2 text-gray-700 dark:text-neutral-300">
                <h1 class="text-3xl font-bold">Heading 1</h1>
                <h2 class="text-2xl font-semibold">Heading 2</h2>
                <h3 class="text-xl font-semibold">Heading 3</h3>
                <p class="text-sm">Body small</p>
                <p>Body default</p>
                <p class="text-lg">Body large</p>
              </div>
        </section>
        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Buttons</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                <button class="inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-gray-800 text-white px-3 py-2 text-sm hover:bg-gray-700 focus:outline-hidden focus:ring-2 focus:ring-gray-600 dark:bg-neutral-700 dark:hover:bg-neutral-600">Primary</button>
                <button class="inline-flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 px-3 py-2 text-sm hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">Secondary</button>
                <button class="inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-blue-600 text-white px-3 py-2 text-sm hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-600">Blue</button>
                <button class="inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-teal-600 text-white px-3 py-2 text-sm hover:bg-teal-700 focus:outline-hidden focus:ring-2 focus:ring-teal-600">Teal</button>
                <button class="inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-red-600 text-white px-3 py-2 text-sm hover:bg-red-700 focus:outline-hidden focus:ring-2 focus:ring-red-600">Danger</button>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Inputs</h2>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <input class="block w-full rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" placeholder="Text input" />
                <select class="block w-full rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300">
                    <option>Option 1</option>
                    <option>Option 2</option>
                </select>
                <textarea rows="3" class="block w-full rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" placeholder="Textarea"></textarea>
                <label class="flex items-center gap-x-2 text-sm text-gray-700 dark:text-neutral-300">
                    <input type="checkbox" class="shrink-0 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    Checkbox
                </label>
                <label class="flex items-center gap-x-2 text-sm text-gray-700 dark:text-neutral-300">
                    <input type="radio" name="radio-demo" class="shrink-0 border-gray-300 text-blue-600 focus:ring-blue-500" />
                    Radio
                </label>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Cards</h2>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:bg-neutral-800 dark:border-neutral-700">
                    <h3 class="font-semibold text-gray-800 dark:text-neutral-200">Card title</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">Supporting text for the card content.</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:bg-neutral-800 dark:border-neutral-700">
                    <h3 class="font-semibold text-gray-800 dark:text-neutral-200">Card title</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">Supporting text for the card content.</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:bg-neutral-800 dark:border-neutral-700">
                    <h3 class="font-semibold text-gray-800 dark:text-neutral-200">Card title</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">Supporting text for the card content.</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Table</h2>
            <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200 dark:border-neutral-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Title</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:bg-neutral-900 dark:divide-neutral-800">
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-800 dark:text-neutral-200">Jane Cooper</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-neutral-400">Regional Paradigm Technician</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-800 dark:text-neutral-200">Cody Fisher</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-neutral-400">Product Directives Officer</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Inactive</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Dropdown</h2>
            <div class="mt-3">
                <div class="hs-dropdown inline-flex [--placement:bottom-left]">
                    <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        Menu
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="hs-dropdown-menu hidden z-10 mt-2 w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-md dark:bg-neutral-900 dark:border-neutral-800">
                        <a href="#" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800">Item 1</a>
                        <a href="#" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800">Item 2</a>
                        <a href="#" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-neutral-800">Item 3</a>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Tabs</h2>
            <div class="mt-3">
                <nav class="flex gap-x-2 border-b border-gray-200 dark:border-neutral-700" aria-label="Tabs">
                    <button type="button" data-hs-tab="#tab-1" class="hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-2 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-600 hover:text-blue-600 focus:outline-hidden focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400">Tab 1</button>
                    <button type="button" data-hs-tab="#tab-2" class="hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-2 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-600 hover:text-blue-600 focus:outline-hidden focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400">Tab 2</button>
                </nav>
                <div class="mt-3">
                    <div id="tab-1" role="tabpanel">Content 1</div>
                    <div id="tab-2" role="tabpanel" class="hidden">Content 2</div>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Alerts</h2>
            <div class="mt-3 space-y-2">
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">Success alert</div>
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">Info alert</div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Warning alert</div>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">Error alert</div>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Breadcrumbs</h2>
            <nav class="mt-3 flex items-center text-sm text-gray-600 dark:text-neutral-400" aria-label="Breadcrumb">
                <a href="#" class="hover:text-gray-900 dark:hover:text-neutral-200">Home</a>
                <span class="mx-2">/</span>
                <a href="#" class="hover:text-gray-900 dark:hover:text-neutral-200">Library</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500 dark:text-neutral-500">Data</span>
            </nav>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Pagination</h2>
            <div class="mt-3">
                <nav class="flex items-center gap-x-1" aria-label="Pagination">
                    <a href="#" class="inline-flex items-center justify-center size-9 rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">«</a>
                    <a href="#" class="inline-flex items-center justify-center size-9 rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">1</a>
                    <a href="#" class="inline-flex items-center justify-center size-9 rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">2</a>
                    <a href="#" class="inline-flex items-center justify-center size-9 rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">3</a>
                    <a href="#" class="inline-flex items-center justify-center size-9 rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">»</a>
                </nav>
            </div>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 dark:text-neutral-200">Modal</h2>
            <div class="mt-3">
                <button type="button" class="inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-blue-600 text-white px-3 py-2 text-sm hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-600" data-hs-overlay="#demo-modal">Open modal</button>
                <div id="demo-modal" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-800">
                            <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                <h3 class="font-semibold text-gray-800 dark:text-white">Modal title</h3>
                                <button type="button" class="inline-flex justify-center items-center w-7 h-7 rounded-full text-sm font-semibold border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700" data-hs-overlay="#demo-modal">✕</button>
                            </div>
                            <div class="p-4 text-sm text-gray-600 dark:text-neutral-300">This is a Preline modal.</div>
                            <div class="flex items-center justify-end gap-x-2 p-4 border-t dark:border-neutral-700">
                                <button type="button" class="inline-flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 px-3 py-2 text-sm hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700" data-hs-overlay="#demo-modal">Cancel</button>
                                <button type="button" class="inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-blue-600 text-white px-3 py-2 text-sm hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-600">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-volt-app>

