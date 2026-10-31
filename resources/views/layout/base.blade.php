<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="relative min-h-full">

<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="/laravolt/assets/images/favicon.ico">

    @stack('meta')

    @laravoltStyles
    <style>
        /* Keep pinch-zoom usable: 16px form controls stop iOS auto-zoom on focus. */
        @media (max-width: 639px) {
            input, select, textarea { font-size: 16px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    @stack('style')
    @stack('head')

    @laravoltScripts
</head>

<body
    class="bg-gray-50 dark:bg-neutral-900 text-gray-800 dark:text-neutral-200 {{ $bodyClass ?? '' }} @yield('body.class')">
    {{ $slot }}

    @stack('script')
    @stack('body')
    @basictableInline('.basictable')
</body>

</html>
