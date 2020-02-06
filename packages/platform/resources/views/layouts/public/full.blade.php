@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--full" data-position="center">
        <div class="tablet or lower hidden"></div>
        <div class="content ui container">
            <div class="ui grid stackable stretched centered">
                <div class="six wide column middle aligned">
                    <div class="ui segment center aligned p-2 rad-10">
                        @yield('content')
                    </div>
                </div>
                <div class="eight wide column mobile hidden middle aligned">
                    <lottie-player
                            src="{{ config('laravolt.ui.animation') }}"  background="transparent" class="mobile hidden" style="height: 100%; width: 100%;"  speed="1" loop  autoplay >
                    </lottie-player>
                </div>
            </div>
        </div>
        <div class="tablet or lower hidden"></div>
    </div>
@endsection
