@extends('ui::layouts.base')

@section('body')
    <div class="layout--app">

        @include('ui::menu.topbar')
        @include('ui::menu.sidebar')

        <div class="content">
            <div class="content__inner">
                <div class="ui container-fluid content__body p-1">
                    @yield('content')
                </div>

            </div>
        </div>
    </div>
@endsection
