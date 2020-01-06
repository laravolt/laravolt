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
