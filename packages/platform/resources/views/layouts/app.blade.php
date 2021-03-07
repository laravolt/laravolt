@extends('laravolt::layouts.base')

@section('body')
    <div class="layout--app">

        @include('laravolt::menu.topbar')
        @include('laravolt::menu.sidebar')

        <main class="content">

            <div class="content__inner">
                <div class="ui two column grid content__body p-x-3 p-y-1 shadow m-b-0" style="background: #FFF">
                    <div class="column middle aligned">
                        <h2 class="ui header">
                            @yield('page.title')
                        </h2>
                    </div>
                    <div class="column right aligned">
                        @yield('page.actions')
                    </div>
                </div>

                <div class="ui container-fluid content__body p-3">
                    @yield('content')
                </div>

            </div>
        </main>
    </div>
@endsection
