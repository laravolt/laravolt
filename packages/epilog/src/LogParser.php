<?php

namespace Laravolt\Epilog;

use Illuminate\Support\Str;

/**
 * Class     LogParser.
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LogParser
{
    const REGEX_DATE_PATTERN = '\d{4}(-\d{2}){2}';
    const REGEX_TIME_PATTERN = '\d{2}(:\d{2}){2}';
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Parsed data.
     *
     * @var array
     */
    protected static $parsed = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse file content.
     *
     * @param string $raw
     *
     * @return array
     */
    public static function parse($raw)
    {
        self::$parsed = [];
        [$headings, $data] = self::parseRawData($raw);

        // @codeCoverageIgnoreStart
        if (!is_array($headings)) {
            return self::$parsed;
        }
        // @codeCoverageIgnoreEnd

        foreach ($headings as $heading) {
            for ($i = 0, $j = count($heading); $i < $j; $i++) {
                self::populateEntries($heading, $data, $i);
            }
        }

        unset($headings, $data);

        return array_reverse(self::$parsed);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse raw data.
     *
     * @param string $raw
     *
     * @return array
     */
    private static function parseRawData($raw)
    {
        $pattern = '/\['.self::REGEX_DATE_PATTERN.' '.self::REGEX_TIME_PATTERN.'\].*/';
        preg_match_all($pattern, $raw, $headings);
        $data = preg_split($pattern, $raw);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        return [$headings, $data];
    }

    /**
     * Populate entries.
     *
     * @param array $heading
     * @param array $data
     * @param int   $key
     */
    private static function populateEntries($heading, $data, $key)
    {
        foreach (config('laravolt.epilog.levels') as $level) {
            if (self::hasLogLevel($heading[$key], $level)) {
                self::$parsed[] = [
                    'level' => $level,
                    'header' => $heading[$key],
                    'stack' => $data[$key],
                ];
            }
        }
    }

    /**
     * Check if header has a log level.
     *
     * @param string $heading
     * @param string $level
     *
     * @return bool
     */
    private static function hasLogLevel($heading, $level)
    {
        return Str::contains($heading, strtoupper(".{$level}:"));
    }
}
