<!DOCTYPE html>
<html lang="{{ config('app.locale') }}"
      data-font-size="{{ config('laravolt.ui.font_size') }}"
      data-theme="{{ config('laravolt.ui.theme') }}"
      data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
      data-spa="{{ config('laravolt.platform.features.spa') }}"
>
<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8"/>
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta content="no-cache">

    @stack('meta')

    @laravoltStyles
    @livewireStyles

    @stack('style')
    @stack('head')

    @laravoltScripts
</head>

<body class="{{ $bodyClass ?? '' }} @yield('body.class')">

    {{ $slot }}

    @livewireScripts
    @stack('script')
    @stack('body')
</body>
</html>
