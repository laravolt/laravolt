@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--auth is-boxed">
        <div class="layout--auth__container">
            <div class="x-laravolt::inspire"
                 style="background-image: url('{!! asset('laravolt/img/wallpaper/animated-svg/blue.svg') !!}')"
            >
                <div class="x-laravolt::inspire__content">
                    <div class="x-laravolt::inspire__text">@include('laravolt::components.inspire')</div>
                </div>
            </div>


            <div class="x-laravolt::auth">
                <div class="x-laravolt::auth__content">

                    <x-laravolt::volt-brand-image class="ui image centered"></x-laravolt::volt-brand-image>
                    <div class="ui divider hidden"></div>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
