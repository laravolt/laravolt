<?php

if (!function_exists('platform_path')) {
    /**
     * Get Laravolt platform absolute directory path.
     *
     * @param string $path
     *
     * @return string
     */
    function platform_path(string $path): string
    {
        return realpath(__DIR__.'/../'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
}

if (!function_exists('is_sqlite')) {
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
