@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--full" data-position="center">
        <div class="tablet or lower hidden"></div>
        <div class="content">
            <div class="ui segment tall stacked center aligned p-2 bg-white-90">
                @include('laravolt::components.brand-image', ['class' => 'tiny centered'])
                <h2 class="ui header">
                    {{ config('app.name') }}
                    <div class="sub header">{{ config('app.description') }}</div>
                </h2>

                <div class="ui divider hidden section"></div>

                @yield('content')
            </div>
        </div>
        <div class="tablet or lower hidden"></div>
    </div>
@endsection

@php(\Stolz\Assets\Laravel\Facade::group('laravolt')->add('vegas'))

@push('script')
    <script>
        $("body").vegas({
            delay: 10000,
            firstTransitionDuration: 1000,
            transitionDuration: 5000,
            slides: [
                { src: "{{ asset('laravolt/img/landscape/forest.jpg') }}" },
                { src: "{{ asset('laravolt/img/landscape/borobudur.jpg') }}" },
                { src: "{{ asset('laravolt/img/landscape/borobudur-evening.jpg') }}" },
                { src: "{{ asset('laravolt/img/landscape/sky.jpg') }}" },
                { src: "{{ asset('laravolt/img/landscape/sky-2.jpg') }}" },
                { src: "{{ asset('laravolt/img/landscape/bromo-tengger.jpg') }}" }
            ]
        });
    </script>
@endpush
