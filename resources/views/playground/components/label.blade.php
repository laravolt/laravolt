{{-- Labels / Badges — Preline UI showcase --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Labels & Badges</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Badge and label patterns using Preline UI's Tailwind-based styling.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Solid, Soft, Outline, and special shapes</p>
    </div>
    <div class="p-4 md:p-5 space-y-6">
        {{-- Solid Badges --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Solid</p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-600 text-white">Blue</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-600 text-white">Teal</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-600 text-white">Red</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-500 text-white">Yellow</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-indigo-600 text-white">Indigo</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-purple-600 text-white">Purple</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-pink-600 text-white">Pink</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-800 text-white dark:bg-white dark:text-neutral-800">Dark</span>
            </div>
        </div>

        {{-- Soft Badges --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Soft</p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">Blue</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">Teal</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">Red</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500">Yellow</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-800/30 dark:text-indigo-500">Indigo</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-500">Purple</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-800/30 dark:text-pink-500">Pink</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-400">Gray</span>
            </div>
        </div>

        {{-- Outline Badges --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Outline</p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500">Blue</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border border-teal-600 text-teal-600 dark:border-teal-500 dark:text-teal-500">Teal</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border border-red-600 text-red-600 dark:border-red-500 dark:text-red-500">Red</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border border-yellow-500 text-yellow-500">Yellow</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border border-indigo-600 text-indigo-600 dark:border-indigo-500 dark:text-indigo-500">Indigo</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border border-gray-800 text-gray-800 dark:border-neutral-400 dark:text-neutral-400">Dark</span>
            </div>
        </div>

        {{-- Badge with Indicator Dot --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">With indicator dot</p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                    <span class="size-1.5 inline-block rounded-full bg-blue-800 dark:bg-blue-500"></span>
                    Active
                </span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">
                    <span class="size-1.5 inline-block rounded-full bg-teal-800 dark:bg-teal-500"></span>
                    Completed
                </span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500">
                    <span class="size-1.5 inline-block rounded-full bg-yellow-800 dark:bg-yellow-500"></span>
                    Pending
                </span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">
                    <span class="size-1.5 inline-block rounded-full bg-red-800 dark:bg-red-500"></span>
                    Rejected
                </span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-400">
                    <span class="size-1.5 inline-block rounded-full bg-gray-800 dark:bg-neutral-400"></span>
                    Archived
                </span>
            </div>
        </div>

        {{-- Rectangular badges --}}
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-3">Rectangular (rounded-lg)</p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium bg-blue-600 text-white">Default</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-400">Neutral</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium border border-gray-200 text-gray-800 dark:border-neutral-700 dark:text-neutral-400">Bordered</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">Success</span>
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">Error</span>
            </div>
        </div>
    </div>
</div>
