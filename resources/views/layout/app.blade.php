<x-volt-base :title="$title ?? ''">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    <main class="lg:ps-65 pt-15 pb-10 sm:pb-16">
        <div class="p-2 sm:p-5 sm:py-0 md:pt-5 space-y-5">
            {{ $slot ?? null }}
        </div>
    </main>

    <footer class="lg:ps-65 h-10 sm:h-16 absolute bottom-0 inset-x-0">
        <div class="p-2 sm:p-5 flex justify-between items-center">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-neutral-500">
                &copy; {{ now()->year }} {{ config('app.name') }}.
            </p>
        </div>
    </footer>
</x-volt-base>
