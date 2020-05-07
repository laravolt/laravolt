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
     * @return string
     * @throws Throwable
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

if (!function_exists('readable_number')) {
    function readable_number(float $value, int $precision = 1): string
    {
        $thresholds = [
            '' => 900,
            'K' => 900000,
            'M' => 900000000,
            'B' => 900000000000,
            'T' => 90000000000000,
        ];

        $default = '900T+';

        foreach ($thresholds as $suffix => $threshold) {
            if ($value < $threshold) {
                $formattedNumber = number_format($value / ($threshold / $thresholds['']), $precision);
                $cleanedNumber = (strpos($formattedNumber, '.') === false)
                    ? $formattedNumber
                    : rtrim(rtrim($formattedNumber, '0'), '.');

                return $cleanedNumber.$suffix;
            }
        }

        return $default;
    }
}
