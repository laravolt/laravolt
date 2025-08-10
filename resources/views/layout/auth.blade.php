<x-volt-base>
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md">
            <div class="flex justify-center mb-6">
                <x-volt-brand-image/>
            </div>

            <main up-main="root" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 dark:bg-neutral-800 dark:border-neutral-700">
                {{ $slot }}
                @stack('main')
            </main>
        </div>
    </div>
</x-volt-base>
