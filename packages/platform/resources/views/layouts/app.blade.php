@extends('laravolt::layouts.base')

@section('body')
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <main class="content">

            <div class="content__inner">
                <div class="ui container-fluid content__body p-4">
                    @yield('content')
                </div>

            </div>
        </main>
    </div>
@endsection
