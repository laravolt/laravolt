<?php

namespace Laravolt\Epilog;

use Dubture\Monolog\Reader\LogReader;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Epilog
{

    private $filesystem;

    private $basePath;

    /**
     * Epilog constructor.
     */
    public function __construct()
    {
        $this->basePath = storage_path('logs');
        $adapter = new Local($this->basePath);
        $this->filesystem = new Filesystem($adapter);
    }

    public function files($year = null, $month = null)
    {
        $path = '';
        if ($year) {
            $path .= $year;
        }
        if ($month) {
            $path .= DIRECTORY_SEPARATOR.$month;
        }

        return collect($this->filesystem->listContents($path, true))
            ->filter(function ($file) {
                return (array_get($file, 'type') === 'file') && (array_get($file, 'extension') === 'log');
            })
            ->sortByDesc('timestamp');
    }

    public function logs($path)
    {
        if (!$this->filesystem->has($path)) {
            return [];
        }

        $reader = new LogReader($this->basePath.DIRECTORY_SEPARATOR.$path);

        $logs = [];
        foreach ($reader as $line) {
            if (!empty($line)) {
                $logs[] = $line;
            }
        }

        return $logs;
    }
}
