<?php

namespace Laravolt\Platform\Services;

class LaravoltBladeDirectives
{
    public static function scripts($expression)
    {
        $calendarLang = json_encode(form_calendar_text());

        return <<<EOF
{!! Asset::js() !!}
{!! Asset::group('laravolt')->js() !!}
<script src="{{ mix('js/vendor.js', 'laravolt') }}"></script>
<script>
    $.fn.calendar.settings.text = $calendarLang;
</script>
<script src="{{ mix('js/platform.js', 'laravolt') }}"></script>
EOF;
    }

    public static function styles($expression)
    {
        return <<<EOF
<style>
    :root {
        --app-accent-color: var(--{{ config('laravolt.ui.color') }});
        --app-login-background: url('{{ url(config('laravolt.ui.login_background')) }}');
    }
</style>

<link rel="stylesheet" type="text/css" href="{{ mix('semantic/semantic.min.css', 'laravolt') }}"/>
<link rel="stylesheet" type="text/css" href="{{ mix('css/all.css', 'laravolt') }}"/>
EOF;
    }
}
