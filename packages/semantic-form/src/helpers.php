<?php

if (!function_exists('form')) {
    /**
     * @return \Laravolt\SemanticForm\SemanticForm
     */
    function form()
    {
        return app('semantic-form');
    }
}

if (!function_exists('form_escape')) {
    /**
     * Escape HTML special characters in a string.
     *
     * @param \Illuminate\Contracts\Support\Htmlable|string $value
     *
     * @return string
     */
    function form_escape($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('form_html_attributes')) {
    /**
     * Convert array of HTML attributes into string.
     *
     * @param array $attributes
     *
     * @return string
     */
    function form_html_attributes(array $attributes)
    {
        $result = '';
        foreach ($attributes as $attribute => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $value = form_escape($value);
            $result .= " {$attribute}=\"{$value}\"";
        }

        return $result;
    }
}

if (!function_exists('form_calendar_text')) {
    /**
     * Mendapatkan localization string untuk ditampilkan di calendar (datepicker, datetimepicker).
     *
     * @return array
     */
    function form_calendar_text(): array
    {
        $now = \Carbon\Carbon::now();
        $startOfWeek = $now->startOfWeek(\Carbon\Carbon::SUNDAY);
        $days = $months = $monthsShort = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfWeek->isoFormat('dd');
            $startOfWeek->addDay();
        }
        for ($i = 1; $i <= 12; $i++) {
            $day = \Carbon\Carbon::createFromDate(2020, $i);
            $months[] = $day->isoFormat('MMMM');
            $monthsShort[] = $day->isoFormat('MMM');
        }
        $localization = [
            'days' => $days,
            'months' => $months,
            'monthsShort' => $monthsShort,
            'today' => __('Hari ini'),
            'now' => __('Sekarang'),
        ];

        return $localization;
    }
}
