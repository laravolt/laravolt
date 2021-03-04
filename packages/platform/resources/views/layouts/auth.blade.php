@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--split">

        <div class="x-inspire">
            <div class="x-inspire__content">
                <div class="ui container text text-center p-4">
                    @include('laravolt::components.inspire')
                </div>
            </div>
        </div>


        <div class="x-auth">
            <div class="x-auth__content p-4">

                <img
                        src="{{ config('laravolt.ui.brand_image') }}"
                        alt=""
                        class="ui image tiny centered"
                >

                <div class="ui divider hidden"></div>

                @yield('content')

                <div class="ui divider hidden m-b-5"></div>

            </div>
        </div>
    </div>
@endsection
