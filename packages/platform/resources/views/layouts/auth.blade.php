@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--auth is-boxed">
        <div class="layout--auth__container">
            <div class="x-inspire"
                 style="background-image: url('{!! config('laravolt.platform.background_login') !!}')"
            >
                <div class="x-inspire__content">
                    <div class="x-inspire__text">@include('laravolt::components.inspire')</div>
                </div>
            </div>


            <div class="x-auth">
                <div class="x-auth__content">

                    <x-laravolt::brand-image class="ui image centered" />
                    <div class="ui divider hidden"></div>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
