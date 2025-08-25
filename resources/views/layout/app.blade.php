<x-volt-base :title="$title">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    <div class="w-full pt-14 lg:ps-64" up-main="root">
        @if (! empty($title))
            @include('laravolt::menu.actionbar')
        @endif

        <main class="p-2 sm:p-5 sm:py-0 md:pt-5 space-y-3">
            {{ $slot }}
            @stack('main')
        </main>
    </div>
</x-volt-base>