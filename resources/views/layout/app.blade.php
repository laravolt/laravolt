<x-volt-base :title="$title">
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <main class="content" up-main>

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
