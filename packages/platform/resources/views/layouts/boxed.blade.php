@extends('ui::layouts.base')

@section('body')
    <div class="ui container">
        @include('ui::menu.topbar')

        <div class="layout--app layout--boxed">
            @include('ui::menu.sidebar')

            <div class="content">
                <div class="content__inner">
                    <div class="ui container-fluid content__body p-1">
                        @yield('content')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
