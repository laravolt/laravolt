<?php

if (!function_exists('format_localized')) {
    function format_localized($date)
    {
        if($date instanceof \Carbon\Carbon) {
            return $date->formatLocalized('%d %B %Y');
        }

        return $date;
    }
}

if (!function_exists('sui_pagination')) {

    /**
     * @param $collection
     * @return \Laravolt\Support\Pagination\SemanticUiPagination
     */
    function sui_pagination($collection)
    {
        return new \Laravolt\Support\Pagination\SemanticUiPaginator($collection);
    }
}

if (!function_exists('render')) {

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return string
     */
    function render($view = null, $data = [], $mergeData = [])
    {
        return view($view, $data, $mergeData)->render();
    }
}
