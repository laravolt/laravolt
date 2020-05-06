@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--split" data-position="left">
        <div class="content">
            <div class="content--inner">
                <div class="ui segment basic center aligned">
                    <a href="{{ route('auth::login') }}">
                        @include('laravolt::components.brand-image', ['class' => 'tiny centered'])
                        <h2 class="ui header">
                            {{ config('app.name') }}
                            <div class="sub header">{{ config('app.description') }}</div>
                        </h2>
                    </a>
                </div>

                <div class="ui segment p-2">
                    @yield('content')
                </div>

            </div>
        </div>
        <div class="tablet or lower hidden">

        </div>
    </div>
@endsection
