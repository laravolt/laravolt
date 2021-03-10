<x-laravolt::layout.base :title="$title">
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <main class="content">

            <div class="content__inner">
                <div class="ui two column grid content__body p-x-3 p-y-1 shadow m-b-0" style="background: #FFF">
                    <div class="column middle aligned">
                        <div class="ui breadcrumb" style="text-transform: uppercase; font-size: .9em; font-weight: bold">
                            <a href="" class="section">Lookup</a>
                            <span class="divider">/</span>
                        </div>
                        <h3 class="ui header m-t-xs">
                            {{ $title }}
                        </h3>
                    </div>
                    <div class="column right aligned middle aligned">
                        {{ $actions ?? '' }}
                    </div>
                </div>

                <div class="ui container-fluid content__body p-3">
                    {{ $slot }}
                </div>

            </div>
        </main>
    </div>
</x-laravolt::layout.base>
