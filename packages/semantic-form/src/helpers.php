<?php

if (! function_exists('form')) {
    /**
     * @return \Laravolt\SemanticForm\SemanticForm
     */
    function form()
    {
        return app('semantic-form');
    }
}

if (! function_exists('form_escape')) {
    /**
     * Escape HTML special characters in a string.
     *
     * @param \Illuminate\Contracts\Support\Htmlable|string $value
     *
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
     * Convert array of HTML attributes into string
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
