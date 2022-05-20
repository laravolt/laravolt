<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" data-font-size="{{ config('laravolt.ui.font_size') }}">
<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8"/>
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta content="no-cache">
    <meta name="spa-enabled" content="{{ config('laravolt.platform.features.spa') }}">

    @stack('meta')

    <style>
        :root {
            --app-accent-color: var(--{{ config('laravolt.ui.color') }});
            --app-login-background: url('{{ url(config('laravolt.ui.login_background')) }}');
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ mix('semantic/semantic.min.css', 'laravolt') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ mix('css/all.css', 'laravolt') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}"/>

    @stack('style')
    @stack('head')
    {!! Asset::group('laravolt')->css() !!}
    {!! Asset::css() !!}

    <script src="{{ mix('js/vendor.js', 'laravolt') }}"></script>

    <script>
        $.fn.calendar.settings.text = @json(form_calendar_text());
    </script>

    <script defer src="{{ mix('js/platform.js', 'laravolt') }}"></script>
    {!! Asset::group('laravolt')->js() !!}

    @livewireStyles
    <script defer src="{{ mix('js/app.js') }}"></script>
</head>

<body data-theme="{{ config('laravolt.ui.theme') }}" class="{{ $bodyClass ?? '' }} @yield('body.class')">

    {{ $slot }}

    {!! Asset::js() !!}
    @livewireScripts
    @stack('script')
    @stack('body')

    <script>
        up.fragment.config.runScripts = true;
        up.fragment.config.navigateOptions.cache = false;
        let firstTimeVisit = true;
        up.compiler('main.content', function (element) {
            Laravolt.init($(element));
            firstTimeVisit = !firstTimeVisit;
            if (!firstTimeVisit && window.Livewire) {
                window.Livewire.restart();
            }
        })
        up.link.config.followSelectors.push('a[href]');
        up.form.config.submitSelectors.push(['form']);

    </script>
</body>
</html>
