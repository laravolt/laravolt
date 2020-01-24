<?php

if (!function_exists('capture_exception')) {
    /**
     * Capture and report exception.
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

if (!function_exists('capture_exception_and_abort')) {
    /**
     * Capture and report exception.
     *
     * @param Throwable $exception
     *
     * @throws Throwable
     *
     * @return string
     */
    function capture_exception_and_abort(Throwable $exception)
    {
        capture_exception($exception);
        if (config('app.debug')) {
            throw $exception;
        }
        abort($exception->getCode(), $exception->getMessage());
    }
}
