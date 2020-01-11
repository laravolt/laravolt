<?php

if (! function_exists('capture_exception')) {
    /**
     * Capture and report exception
     *
     * @param Throwable $exception
     *
     * @return string
     */
    function capture_exception(Throwable $exception)
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
