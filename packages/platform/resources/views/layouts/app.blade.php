@extends('laravolt::layouts.base')

@section('body')
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <main class="content">

            <div class="content__inner">
                <div class="ui container-fluid content__body p-2">
                    @isset($__page)
                    @include('laravolt::components.page-header', $__page ?? [])
                    @endisset

                    @yield('content')
                </div>

            </div>
        </main>
    </div>
@endsection
