<x-volt-base :title="$title">
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <main class="content"
              up-main
              data-font-size="{{ config('laravolt.ui.font_size') }}"
              data-theme="{{ config('laravolt.ui.theme') }}"
              data-accent-color="{{ config('laravolt.ui.color') }}"
        >

            <div class="content__inner">

                @include('laravolt::menu.actionbar')

                <div class="ui container-fluid content__body p-3">
                    {{ $slot }}
                </div>

            </div>

            @stack('main')

        </main>
    </div>
</x-volt-base>
