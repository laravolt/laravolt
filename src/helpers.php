<?php

if (! function_exists('platform_path')) {
    /**
     * Get Laravolt platform absolute directory path.
     *
     * @param  string  $path
     *
     * @return string
     */
    function platform_path(string $path): string
    {
        return realpath(__DIR__.'/../'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
}

if (! function_exists('platform_max_file_upload')) {
    /**
     * Determine max file upload size based on php.ini settings.
     *
     * @param  bool  $shorthand
     *
     * @return int|string
     */
    function platform_max_file_upload($shorthand = false): int|string
    {
        $max_upload = shorthand_to_byte(ini_get('upload_max_filesize'));
        $max_post = shorthand_to_byte(ini_get('post_max_size'));
        $memory_limit = shorthand_to_byte(ini_get('memory_limit'));

        // return the smallest of them, this defines the real limit
        $limit = min($max_upload, $max_post, $memory_limit);

        if ($shorthand) {
            return byte_to_shorthand($limit);
        }

        return $limit;
    }
}

if (! function_exists('shorthand_to_byte')) {
    function shorthand_to_byte($shorthand): int
    {
        $shorthand = trim($shorthand);
        $val = (int) $shorthand;
        $last = strtolower($shorthand[strlen($shorthand) - 1]);

        switch ($last) {
            case 'g':
                $val *= 1073741824;
                break;
            case 'm':
                $val *= 1048576;
                break;
            case 'k':
                $val *= 1024;
                break;
        }

        return $val;
    }
}

if (! function_exists('byte_to_shorthand')) {
    function byte_to_shorthand($size, $precision = 0): string
    {
        static $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size /= $step;
            $i++;
        }

        return round($size, $precision).$units[$i];
    }
}

if (! function_exists('is_sqlite')) {
    /**
     * Determine whether current database connection driver was SQLite or not.
     *
     * @return bool
     */
    function is_sqlite(): bool
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        return $driver === 'sqlite';
    }
}

if (! function_exists('array_to_html_attributes')) {
    function array_to_html_attributes(array $attributes): string
    {
        $tagAttributes = '';

        foreach ($attributes as $attribute => $value) {
            $tagAttributes .= " {$attribute}=\"{$value}\"";
        }

        return $tagAttributes;
    }
}
if (! function_exists('readable_number')) {
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
