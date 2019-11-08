@extends('laravolt::layouts.base')

@section('body')
    <div class="ui container">
        <div class="ui stackable grid centered">
            <div class="eight wide column">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
