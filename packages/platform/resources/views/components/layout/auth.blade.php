<x-laravolt::layout.base>
    <div class="layout--auth is-{!! config('laravolt.ui.login.layout') !!}">
        <div class="layout--auth__container">
            <div class="x-inspire"
                 style="background-image: url('{!! config('laravolt.ui.login.background') !!}')"
            >
                <div class="x-inspire__content">
                    <div class="x-inspire__text">
                        <x-laravolt::inspire/>
                    </div>
                </div>
            </div>


            <div class="x-auth">
                <div class="x-auth__content">

                    <x-laravolt::brand-image class="ui image centered"/>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-laravolt::layout.base>
