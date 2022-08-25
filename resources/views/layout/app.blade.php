<x-volt-base :title="$title">
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <div class="content"
              up-main="root"
        >

            <div class="content__inner">

                @include('laravolt::menu.actionbar')

                <main class="ui container-fluid content__body p-3"
                      up-main="modal"
                      data-font-size="{{ config('laravolt.ui.font_size') }}"
                      data-theme="{{ config('laravolt.ui.theme') }}"
                      data-accent-color="{{ config('laravolt.ui.color') }}"
                      data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
                >
                    {{ $slot }}
                    @stack('main')
                </main>

            </div>
        </div>
    </div>
</x-volt-base>
