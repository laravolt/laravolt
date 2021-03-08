<x-laravolt::layout.base>
    <div class="layout--auth is-boxed">
        <div class="layout--auth__container">
            <div class="x-inspire"
                 style="background-image: url('{!! asset('laravolt/img/wallpaper/animated-svg/blue.svg') !!}')"
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
