<x-volt-base title="">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    <main class="lg:ps-65 pt-15 pb-10 sm:pb-16">
        <div class="p-2 sm:p-5 sm:py-0 md:pt-5 space-y-5">
            @if (! empty($title))
                @include('laravolt::menu.actionbar')
            @endif

            {{ $slot ?? null }}
        </div>
    </main>
</x-volt-base>
