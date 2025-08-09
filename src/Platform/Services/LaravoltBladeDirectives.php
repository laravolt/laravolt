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
                window.__calendarText = $calendarLang;
            </script>
            HTML;
            $calendarLocalization = $calendarLang;
        }

        return <<<EOF
{!! Asset::js() !!}
{!! Asset::group('laravolt')->js() !!}
<?php if(config('laravolt.platform.features.spa')): ?>
<script src="{{ mix('js/vendor-spa.js', 'laravolt') }}"></script>
<?php else: ?>
<script src="{{ mix('js/vendor.js', 'laravolt') }}"></script>
<?php endif; ?>
$calendarLocalization
<script src="{{ mix('js/platform.js', 'laravolt') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/preline@2.5.1/dist/preline.min.js"></script>
EOF;
    }

    public static function styles($expression)
    {
        return <<<'EOF'
{!! Asset::group('laravolt')->css() !!}
{!! Asset::css() !!}

<style>
    :root {
        --app-accent-color: var(--{{ config('laravolt.ui.color') }});
        --app-login-background: url('{{ url(config('laravolt.ui.login_background')) }}');
    }
</style>

<script src="https://cdn.tailwindcss.com"></script>
<script>
  window.tailwind = window.tailwind || {};
  tailwind.config = {
    darkMode: 'class',
    theme: { extend: {} },
    safelist: [
      'bg-teal-600','hover:bg-teal-700','focus:ring-teal-500','text-teal-600','border-teal-600',
      'bg-blue-600','hover:bg-blue-700','focus:ring-blue-500','text-blue-600','border-blue-600',
      'bg-red-600','hover:bg-red-700','focus:ring-red-500','text-red-600','border-red-600',
      'bg-gray-100','bg-white','text-gray-700','text-gray-800','border-gray-200','shadow-sm',
      'hidden'
    ]
  };
</script>

<style>.sidebar__menu > .ui.attached.menu:not(.tabular):not(.text) {border: unset}.panel.x-suitable .ui.bottom.attached.menu .item {border-width: 1px;border-color: #8090a0}</style>
EOF;
    }
}
