@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--split">
        <div data-role="inspire">
            @include('laravolt::components.inspire')
        </div>
        <div data-role="main" class="ui pattern diagonal-lines">
            <div data-role="content">
                <div class="p-3">

                    @include('laravolt::components.brand')
                    <div class="ui divider hidden m-b-4"></div>

                    @yield('content')

                    <div class="ui divider hidden section"></div>
                    <div class="ui divider hidden section"></div>
                    <div class="ui divider hidden section"></div>
                    <div class="ui divider hidden section"></div>
                    <div class="ui divider hidden section"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
