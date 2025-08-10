<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="relative min-h-full"
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

    <script>
        const html = document.documentElement;
        const stored = localStorage.getItem('hs_theme');
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const initial = stored || '{{ config('laravolt.ui.theme') }}';
        const isLightOrAuto = initial === 'light' || (initial === 'auto' && !prefersDark);
        const isDarkOrAuto = initial === 'dark' || (initial === 'auto' && prefersDark);
        if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
        else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
        else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
        else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
    </script>

    @stack('meta')

    @laravoltStyles
    @livewireStyles

    @stack('style')
    @stack('head')

    @laravoltScripts
</head>

<body class="bg-gray-50 dark:bg-neutral-900 {{ $bodyClass ?? '' }} @yield('body.class')">

    {{ $slot }}

    @livewireScripts
    @stack('script')
    @stack('body')
</body>
</html>
