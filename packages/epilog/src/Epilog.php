<?php

namespace Laravolt\Epilog;

use Illuminate\Support\Arr;
use Laravolt\Epilog\MonologParser\Reader\LogReader;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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
                return (Arr::get($file, 'type') === 'file') && (Arr::get($file, 'extension') === 'log');
            })
            ->sortByDesc('timestamp');
    }

    public function logs($path)
    {
        if (!$this->filesystem->has($path)) {
            return [];
        }

        $reader = new LogReader($this->basePath.DIRECTORY_SEPARATOR.$path);
        $levels = config('laravolt.epilog.levels');
        $logs = [];
        foreach ($reader as $line) {
            if (!empty($line)) {
                $line['class'] = $levels[$line['level']]['class'] ?? '';
                $logs[] = $line;
            }
        }

        return collect($logs)->sortByDesc('date');
    }
}
