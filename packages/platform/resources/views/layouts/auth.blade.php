@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--split">

        <div class="x-inspire" style="background-image: url('{!! asset('laravolt/img/wallpaper/animated-svg/blue.svg') !!}')">
            <div class="x-inspire__content">
                <div class="ui container text text-center p-4">
                    @include('laravolt::components.inspire')
                </div>
            </div>
        </div>


        <div class="x-auth">
            <div class="x-auth__content p-5">

                <x-volt-brand-image class="ui image centered"></x-volt-brand-image>
                <div class="ui divider hidden"></div>

                @yield('content')

                <div class="ui divider hidden m-b-5"></div>

            </div>
        </div>
    </div>
@endsection
