<!DOCTYPE html>
<html lang="{{ config('app.locale') }}"
      data-font-size="{{ config('laravolt.ui.font_size') }}"
      data-theme="{{ config('laravolt.ui.theme') }}"
      data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
      data-spa="{{ config('laravolt.platform.features.spa') }}"
      class="h-full"
>
<head>
    <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>

    <meta charset="UTF-8"/>
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta content="no-cache">

    @stack('meta')

    <!-- TailwindCSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Preline UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/preline@3.2.3/dist/preline.css">
    
    <!-- TailwindCSS Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe', 
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    @laravoltStyles
    @livewireStyles

    @stack('style')
    @stack('head')

    @laravoltScripts
</head>

<body class="bg-gray-50 dark:bg-slate-900 {{ $bodyClass ?? '' }} @yield('body.class')">

    {{ $slot }}

    <!-- Preline UI JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/preline@3.2.3/dist/preline.js"></script>
    
    <!-- Initialize Preline Components -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.HSStaticMethods.autoInit();
        });
        
        // Livewire compatibility
        @if(class_exists('Livewire\Livewire'))
        document.addEventListener('livewire:navigated', () => {
            window.HSStaticMethods.autoInit();
        });
        document.addEventListener('livewire:navigating', () => {
            // Cleanup Preline components before navigation
            if (window.HSStaticMethods && window.HSStaticMethods.autoInit) {
                window.HSStaticMethods.autoInit();
            }
        });
        @endif
    </script>

    @livewireScripts
    @stack('script')
    @stack('body')
</body>
</html>
