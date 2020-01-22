@extends('laravolt::layouts.base')
@section('body')

    <div class="layout--split" data-position="left">
        <div class="content">
            <div class="content--inner ui segment tall stacked center aligned p-2">
                @include('laravolt::components.brand-image', ['class' => 'tiny centered'])
                <h2 class="ui header" style="font-weight: 400">
                    {{ config('app.name') }}
                    <div class="sub header">{{ config('app.description') }}</div>
                </h2>

                <div class="ui divider hidden section"></div>

                @yield('content')
            </div>
        </div>
        <div class="tablet or lower hidden" bg-slideshow>

        </div>
    </div>
@endsection

@php(\Stolz\Assets\Laravel\Facade::group('laravolt')->add('vegas'))

@push('script')
    <script>
        $("[bg-slideshow]").vegas({
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
