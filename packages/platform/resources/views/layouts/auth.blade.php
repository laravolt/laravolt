@extends('ui::layouts.base', ['bodyClass' => 'auth'])
@section('body')
    <div class="ui divider hidden section"></div>
    <div class="ui grid stackable">
        <div class="six wide column centered">
            <div class="ui segments center aligned raised">
                <div class="ui segment center aligned very padded">

                    <h2 class="ui header" style="text-transform: uppercase; font-weight: 400">
                        @include('ui::components.brand-image', ['class' => 'mini centered'])
                        {{ config('app.name') }}</h2>
                </div>
                <div class="ui segment very padded center aligned">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection

