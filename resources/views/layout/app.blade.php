<x-volt-base :title="$title">
    @include('laravolt::menu.topbar')
    @include('laravolt::menu.sidebar')

    <main id="content" class="lg:ps-64 pt-16 pb-10 sm:pb-16"
          up-main="root"
          data-font-size="{{ config('laravolt.ui.font_size') }}"
          data-theme="{{ config('laravolt.ui.theme') }}"
          data-accent-color="{{ config('laravolt.ui.color') }}"
          data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
    >
        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
            @include('laravolt::menu.actionbar')
            <div class="mt-4">
                {{ $slot }}
                @stack('main')
            </div>
        </div>
    </main>
</x-volt-base>
