@extends('laravolt::layouts.base')
@section('body')
    <div style="
        display: flex;
        align-items: center;
        min-height: 100vh;
        justify-content: center;"
    >
        <div style="flex: 1;">
            <div class="ui container text">
                @yield('content')
            </div>
        </div>
    </div>
@endsection

