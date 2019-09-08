<?php

if (!function_exists('platform_path')) {
    /**
     * Get Laravolt platform absolute directory path.
     *
     * @param string $path
     *
     * @return string
     */
    function platform_path(string $path)
    {
        return realpath(__DIR__.'/../'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
}
