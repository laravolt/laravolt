<?php

namespace Laravolt\Thunderclap;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Helper functions for the Packager commands.
 *
 * @author JeroenG
 *
 **/
class FileTransformer
{
    /**
     * The filesystem handler.
     *
     * @var object
     */
    protected $files;

    /**
     * Create a new instance.
     *
     * @param Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Setting custom formatting for the progress bar.
     *
     * @param object $bar Symfony ProgressBar instance
     *
     * @return object $bar Symfony ProgressBar instance
     */
    public function barSetup(ProgressBar $bar)
    {
        // the finished part of the bar
        $bar->setBarCharacter('<comment>=</comment>');

        // the unfinished part of the bar
        $bar->setEmptyBarCharacter('-');

        // the progress character
        $bar->setProgressCharacter('>');

        // the 'layout' of the bar
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% ');

        return $bar;
    }

    /**
     * Open haystack, find and replace needles, save haystack.
     *
     * @param string $oldFile       The haystack
     * @param mixed  $search        String or array to look for (the needles)
     * @param mixed  $replace       What to replace the needles for?
     * @param string $newFile       Where to save, defaults to $oldFile
     * @param bool   $deleteOldFile Whether to delete $oldFile or not
     *
     * @return void
     */
    public function replaceAndSave($oldFile, $search, $replace, $newFile = null, $deleteOldFile = false)
    {
        $newFile = ($newFile === null) ? $oldFile : $newFile;
        $file = $this->files->get($oldFile);
        $replacing = str_replace($search, $replace, $file);
        $this->files->put($newFile, $replacing);

        if ($deleteOldFile) {
            $this->files->delete($oldFile);
        }
    }

    /**
     * Check if the package already exists.
     *
     * @param string $path   Path to the package directory
     * @param string $vendor The vendor
     * @param string $name   Name of the package
     *
     * @return void Throws error if package exists, aborts process
     */
    public function checkExistingPackage($path, $vendor, $name)
    {
        if (is_dir($path.$vendor.'/'.$name)) {
            throw new RuntimeException('Package already exists');
        }
    }

    /**
     * Create a directory if it doesn't exist.
     *
     * @param string $path Path of the directory to make
     *
     * @return void
     */
    public function makeDir($path)
    {
        if (!is_dir($path)) {
            return mkdir($path);
        }
    }
}
