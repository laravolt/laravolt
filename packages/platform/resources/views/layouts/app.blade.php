@extends('laravolt::layouts.base')

@section('body')
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <div class="content">
            <div class="content__inner">
                <div class="ui container-fluid content__body p-1">
                    @isset($__page)
                        @include('laravolt::components.page-header', $__page)
                    @endisset

                    @yield('content')
                </div>

            </div>
        </div>
    </div>
@endsection
