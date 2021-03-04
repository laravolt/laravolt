<!DOCTYPE html>
<html data-font-size="{{ config('laravolt.ui.font_size') }}">
<head>
    <title>@yield('site.title', "Welcome Home") | {{ config('app.name') }}</title>

    <meta charset="UTF-8"/>
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="turbolinks-cache-control" content="no-cache">
    <meta name="turbolinks-enabled" content="{{ config('laravolt.platform.features.turbolinks') }}">

    @stack('meta')

    <link rel="stylesheet" type="text/css" data-turbolinks-track="reload" href="{{ mix('semantic/semantic.min.css', 'laravolt') }}"/>
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
</head>

<body id="body" data-theme="{{ config('laravolt.ui.theme') }}" login-theme="{{ config('laravolt.ui.login_theme') }}" class="{{ $bodyClass ?? '' }}">

@yield('body')

{!! Asset::js() !!}
@stack('script')
@stack('body')
</body>
</html>
