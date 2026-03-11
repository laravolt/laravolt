{{-- Timeline — Activity Feed --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Timeline</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Activity feed pattern with Tailwind CSS — perfect for changelogs, audit logs, and notifications.</p>

<x-volt-panel title="Activity Feed">
    <div>
        {{-- Item 1 --}}
        <div class="flex gap-x-3">
            <div class="relative last:after:hidden after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                <div class="relative size-7 flex justify-center items-center" style="z-index:1">
                    <div class="size-2 rounded-full bg-blue-600 dark:bg-blue-500"></div>
                </div>
            </div>
            <div class="grow pt-0.5 pb-8">
                <h3 class="flex gap-x-1.5 font-semibold text-gray-800 dark:text-white">
                    <svg class="shrink-0 size-4 mt-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Created "Preline UI v4.1.2 Migration Plan"
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">Document created and shared with 3 team members for review.</p>
                <button type="button" class="mt-1 -ms-1 p-1 inline-flex items-center gap-x-2 text-xs rounded-lg border border-transparent text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700">
                    <img class="shrink-0 size-4 rounded-full" src="https://ui-avatars.com/api/?name=Admin&size=32&background=3b82f6&color=fff" alt="Avatar">
                    Admin · 2 hours ago
                </button>
            </div>
        </div>

        {{-- Item 2 --}}
        <div class="flex gap-x-3">
            <div class="relative last:after:hidden after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                <div class="relative size-7 flex justify-center items-center" style="z-index:1">
                    <div class="size-2 rounded-full bg-green-500"></div>
                </div>
            </div>
            <div class="grow pt-0.5 pb-8">
                <h3 class="flex gap-x-1.5 font-semibold text-gray-800 dark:text-white">
                    <svg class="shrink-0 size-4 mt-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    New user <span class="font-normal text-gray-600 dark:text-neutral-400">registered to the platform</span>
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">User "Bayu Hendra" completed registration and email verification.</p>
                <button type="button" class="mt-1 -ms-1 p-1 inline-flex items-center gap-x-2 text-xs rounded-lg border border-transparent text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700">
                    <img class="shrink-0 size-4 rounded-full" src="https://ui-avatars.com/api/?name=BH&size=32&background=10b981&color=fff" alt="Avatar">
                    System · 5 hours ago
                </button>
            </div>
        </div>

        {{-- Item 3 --}}
        <div class="flex gap-x-3">
            <div class="relative last:after:hidden after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                <div class="relative size-7 flex justify-center items-center" style="z-index:1">
                    <div class="size-2 rounded-full bg-amber-500"></div>
                </div>
            </div>
            <div class="grow pt-0.5 pb-8">
                <h3 class="flex gap-x-1.5 font-semibold text-gray-800 dark:text-white">
                    <svg class="shrink-0 size-4 mt-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    Config change <span class="font-normal text-gray-600 dark:text-neutral-400">applied to production</span>
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">Updated APP_ENV from "staging" to "production" and cleared config cache.</p>
                <span class="mt-1 inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Warning: Review required</span>
            </div>
        </div>

        {{-- Item 4 --}}
        <div class="flex gap-x-3">
            <div class="relative last:after:hidden after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                <div class="relative size-7 flex justify-center items-center" style="z-index:1">
                    <div class="size-2 rounded-full bg-gray-400 dark:bg-neutral-600"></div>
                </div>
            </div>
            <div class="grow pt-0.5 pb-0">
                <h3 class="flex gap-x-1.5 font-semibold text-gray-800 dark:text-white">
                    <svg class="shrink-0 size-4 mt-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    System health check <span class="font-normal text-gray-600 dark:text-neutral-400">completed</span>
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">All services healthy. Database: 14ms, Cache: 2ms, Queue: idle.</p>
                <button type="button" class="mt-1 -ms-1 p-1 inline-flex items-center gap-x-2 text-xs rounded-lg border border-transparent text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700">
                    System · 1 day ago
                </button>
            </div>
        </div>
    </div>
</x-volt-panel>
