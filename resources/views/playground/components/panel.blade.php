<h2 class="text-xl font-semibold text-gray-800 mb-2">
    Panel
    <span class="block text-sm font-normal text-gray-500">Semua konten yang merupakan satu kesatuan wajib dibungkus dalam sebuah panel</span>
</h2>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-volt-panel title="Panel">
            <div class="animate-pulse space-y-3">
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="space-y-2">
                    <div class="h-3 bg-gray-200 rounded"></div>
                    <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div>
        <x-volt-panel title="Panel With Icon" icon="rocket">
            <div class="animate-pulse space-y-3">
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="space-y-2">
                    <div class="h-3 bg-gray-200 rounded"></div>
                    <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div class="sm:col-span-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-volt-panel title="Panel With Footer">
                <div class="animate-pulse space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="space-y-2">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
                <x-slot name="footer">
                    Footer
                </x-slot>
            </x-volt-panel>
        </div>
        <div>
            <x-volt-panel title="Panel With Action">
                <div class="animate-pulse space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="space-y-2">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
                <x-slot name="action">
                    <x-volt-link-button url="#" icon="edit" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Edit</x-volt-link-button>
                </x-slot>
                <x-slot name="footer">
                    Footer
                </x-slot>
            </x-volt-panel>
        </div>

    </div>
</div>
