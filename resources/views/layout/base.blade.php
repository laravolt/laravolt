<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="relative min-h-full">

<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta content="no-cache">

    @stack('meta')

    @laravoltStyles
    @livewireStyles

    @stack('style')
    @stack('head')

    @laravoltScripts
</head>

<body
    class="bg-gray-50 dark:bg-neutral-900 text-gray-800 dark:text-neutral-200 {{ $bodyClass ?? '' }} @yield('body.class')">
    {{ $slot }}

    @livewireScripts
    @stack('script')
    @stack('body')
</body>

</html>
