<?php

if (!function_exists('sui_pagination')) {

    /**
     * @param $collection
     * @return \Laravolt\Support\Pagination\SemanticUiPagination
     */
    function sui_pagination($collection)
    {
        return new \Laravolt\Support\Pagination\SemanticUiPagination($collection);
    }
}
