<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
    Panel
    <span class="block text-sm font-normal text-gray-500 dark:text-neutral-400 mt-1">Semua konten yang merupakan satu kesatuan wajib dibungkus dalam sebuah panel</span>
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-volt-panel title="Panel">
            <div class="animate-pulse space-y-4">
                <div class="flex items-center gap-x-4">
                    <div class="size-10 bg-gray-200 rounded-full dark:bg-neutral-700"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-1/2"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700"></div>
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-5/6"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div>
        <x-volt-panel title="Panel With Icon" icon="rocket">
            <div class="animate-pulse space-y-4">
                <div class="flex items-center gap-x-4">
                    <div class="size-10 bg-gray-200 rounded-full dark:bg-neutral-700"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-1/2"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700"></div>
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-5/6"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div>
        <x-volt-panel title="Panel With Footer">
            <div class="animate-pulse space-y-4">
                <div class="flex items-center gap-x-4">
                    <div class="size-10 bg-gray-200 rounded-full dark:bg-neutral-700"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-1/2"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700"></div>
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-5/6"></div>
                </div>
            </div>
            <x-slot name="footer">
                Footer
            </x-slot>
        </x-volt-panel>
    </div>
    <div>
        <x-volt-panel title="Panel With Action">
            <div class="animate-pulse space-y-4">
                <div class="flex items-center gap-x-4">
                    <div class="size-10 bg-gray-200 rounded-full dark:bg-neutral-700"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-1/2"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700"></div>
                    <div class="h-3 bg-gray-200 rounded dark:bg-neutral-700 w-5/6"></div>
                </div>
            </div>
            <x-slot name="action">
                <x-volt-link-button url="#" icon="edit" class="mini">Edit</x-volt-link-button>
            </x-slot>
            <x-slot name="footer">
                Footer
            </x-slot>
        </x-volt-panel>
    </div>
</div>
