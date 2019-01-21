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
