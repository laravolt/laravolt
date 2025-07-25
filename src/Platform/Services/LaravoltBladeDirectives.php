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

<link rel="stylesheet" type="text/css" href="{{ mix('semantic/semantic.min.css', 'laravolt') }}"/>
<link rel="stylesheet" type="text/css" href="{{ mix('css/all.css', 'laravolt') }}"/>
<style>.sidebar__menu > .ui.attached.menu:not(.tabular):not(.text) {border: unset}.panel.x-suitable .ui.bottom.attached.menu .item {border-width: 1px;border-color: #8090a0}</style>
EOF;
    }
}
