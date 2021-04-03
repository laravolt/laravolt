<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" data-font-size="{{ config('laravolt.ui.font_size') }}">
<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8"/>
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="turbolinks-cache-control" content="no-cache">
    <meta name="turbolinks-enabled" content="{{ config('laravolt.platform.features.turbolinks') }}">

    @stack('meta')

    <style>
        :root {
            --app-accent-color: var(--{{ config('laravolt.ui.color') }});
            --app-login-background: url('{{ url(config('laravolt.ui.login_background')) }}');
        }
    </style>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload"
          href="{{ mix('semantic/semantic.min.css', 'laravolt') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ mix('css/all.css', 'laravolt') }}"/>
    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ mix('css/app.css') }}"/>

    @stack('style')
    @stack('head')
    {!! Asset::group('laravolt')->css() !!}
    {!! Asset::css() !!}

    <script data-turbolinks-track="reload" src="{{ mix('js/vendor.js', 'laravolt') }}"></script>

    <script>
        $.fn.calendar.settings.text = @json(form_calendar_text());
    </script>

    <script defer data-turbolinks-track="reload" src="{{ mix('js/platform.js', 'laravolt') }}"></script>
    {!! Asset::group('laravolt')->js() !!}

    <script defer data-turbolinks-track="reload" src="{{ mix('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.1/dist/alpine.min.js" defer></script>

    @livewireStyles
</head>

<body data-theme="{{ config('laravolt.ui.theme') }}" class="{{ $bodyClass ?? '' }}">

{{ $slot }}

{!! Asset::js() !!}
@stack('script')
@stack('body')
@livewireScripts
</body>
</html>
