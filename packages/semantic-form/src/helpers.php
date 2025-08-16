<?php

if (! function_exists('form')) {
    /**
     * Get the form manager instance or specific form builder.
     * 
     * @param string|null $driver
     * @return \Laravolt\SemanticForm\FormManager|mixed
     */
    function form($driver = null)
    {
        $manager = app('form-manager');
        
        return $driver ? $manager->driver($driver) : $manager;
    }
}

if (! function_exists('semantic_form')) {
    /**
     * Get the SemanticForm builder instance.
     * 
     * @return \Laravolt\SemanticForm\SemanticForm
     */
    function semantic_form()
    {
        return app('form-manager')->driver('semantic');
    }
}

if (! function_exists('form_escape')) {
    /**
     * Escape HTML special characters in a string.
     *
     * @param  \Illuminate\Contracts\Support\Htmlable|string  $value
     * @return string
     */
    function form_escape($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('form_html_attributes')) {
    /**
     * Convert array of HTML attributes into string.
     *
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

            if (is_numeric($attribute)) {
                $result .= " {$value}";
            } else {
                $result .= " {$attribute}=\"{$value}\"";
            }
        }

        return $result;
    }
}

if (! function_exists('form_calendar_text')) {
    /**
     * Mendapatkan localization string untuk ditampilkan di calendar (datepicker, datetimepicker).
     */
    function form_calendar_text(): array
    {
        return [
            'days' => ['M', 'S', 'S', 'R', 'K', 'J', 'S'],
            'dayNamesShort' => ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            'dayNames' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'months' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            'monthsShort' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'today' => __('Hari ini'),
            'now' => __('Sekarang'),
            'am' => __('AM'),
            'pm' => __('PM'),
        ];
    }
}
