<x-volt-base :title="$title">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    <div class="w-full pt-14 lg:ps-64" up-main="root">
        @if (! empty($title))
            @include('laravolt::menu.actionbar')
        @endif

        <main class="p-4 sm:p-6 lg:p-8" up-main="modal"
              data-font-size="{{ config('laravolt.ui.font_size') }}"
              data-theme="{{ config('laravolt.ui.theme') }}"
              data-accent-color="{{ config('laravolt.ui.color') }}"
              data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
        >
            {{ $slot }}
            @stack('main')
        </main>
    </div>
</x-volt-base>
