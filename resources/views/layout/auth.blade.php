<x-volt-base>
    <div class="min-h-screen grid lg:grid-cols-2 bg-gray-50 dark:bg-neutral-900">
        <!-- Left: Inspire / Hero -->
        <div class="hidden lg:block relative">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{!! config('laravolt.ui.login_background') !!}')"></div>
            <div class="relative h-full w-full flex items-end p-8 sm:p-10 bg-gradient-to-t from-black/30 to-black/0">
                <div class="max-w-xl text-white/90">
                    <x-volt-inspire />
                </div>
            </div>
        </div>

        <!-- Right: Auth Card -->
        <div class="flex items-center justify-center p-6 sm:p-10">
            <main class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 dark:bg-neutral-800 dark:border-neutral-700" up-main="root">
                <div class="flex justify-center mb-6">
                    <x-volt-brand-image class="h-10" />
                </div>

                {{ $slot }}
                @stack('main')
            </main>
        </div>
    </div>
</x-volt-base>
