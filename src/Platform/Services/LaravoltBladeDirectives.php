<?php

namespace Laravolt\Platform\Services;

class LaravoltBladeDirectives
{
    public static function scripts($expression)
    {
        $calendarLocalization = '';
        if (config('app.locale') === 'id') {
            $calendarLang = json_encode(form_calendar_text());
            $calendarLang = <<<HTML
            <script>
                $.fn.calendar.settings.text = $calendarLang;
            </script>
            HTML;
        }

        return <<<'HTML'
          <!-- Required plugins -->
          <script src="/laravolt/assets/vendor/preline/dist/index.js?v=3.0.1"></script>
          <!-- Clipboard -->
          <script src="/laravolt/assets/vendor/clipboard/dist/clipboard.min.js"></script>
          <script src="/laravolt/assets/js/hs-copy-clipboard-helper.js"></script>
          <!-- Apexcharts -->
          <script src="/laravolt/assets/vendor/lodash/lodash.min.js"></script>
          <script src="/laravolt/assets/vendor/apexcharts/dist/apexcharts.min.js"></script>
          <script src="/laravolt/assets/vendor/preline/dist/helper-apexcharts.js"></script>
          <!-- JS INITIALIZATIONS -->
          <script>
            document.addEventListener("DOMContentLoaded", () => {
              const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.content || "";
            });
          </script>
        HTML;
    }

    public static function styles($expression)
    {
        $accent = config('laravolt.ui.colors.'.config('laravolt.ui.color'), '#3b82f6');

        return <<<HTML
          <!-- Font -->
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

          <!-- CSS HS -->
          <link rel="stylesheet" href="/laravolt/assets/css/main.min.css?v=3.0.1">

          <!-- Theme Check and Update -->
          <script>
            const html = document.querySelector('html');
            const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
            const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
            else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
            else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
            else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
          </script>

          <link rel="stylesheet" href="/laravolt/assets/vendor/apexcharts/dist/apexcharts.css">
          <style type="text/css">
            :root { --accent: {$accent}; --accent-foreground: #ffffff; }
            .btn-accent { background-color: var(--accent) !important; color: var(--accent-foreground) !important; }
            .btn-accent:hover, .btn-accent:focus { filter: brightness(0.95); }
            .btn-accent-soft { color: var(--accent) !important; background-color: color-mix(in srgb, var(--accent) 12%, transparent) !important; }
            .btn-accent-soft:hover, .btn-accent-soft:focus { background-color: color-mix(in srgb, var(--accent) 18%, transparent) !important; }
            .link-accent { color: var(--accent) !important; }
            .link-accent:hover, .link-accent:focus { text-decoration: underline; }
            input[type="checkbox"], input[type="radio"] { accent-color: var(--accent); }
            input:focus, select:focus, textarea:focus {
              border-color: var(--accent) !important;
              box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 20%, transparent) !important;
            }
            .apexcharts-tooltip.apexcharts-theme-light
            {
              background-color: transparent !important;
              border: none !important;
              box-shadow: none !important;
            }
          </style>
        HTML;
    }
}
