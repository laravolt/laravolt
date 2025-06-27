<?php

declare(strict_types=1);

namespace Laravolt\Platform\Scripts;

use Composer\Script\Event;

class ComposerScripts
{
    /**
     * Handle post-install and post-update events.
     *
     * @param Event $event
     * @return void
     */
    public static function postAutoloadDump(Event $event): void
    {
        // Only run if we're in a Laravel project
        if (!self::isLaravelProject()) {
            return;
        }

        self::extractAssets();
    }

    /**
     * Extract Laravolt assets to the appropriate directories.
     *
     * @return void
     */
    public static function extractAssets(): void
    {
        $vendorDir = self::getVendorDir();
        $laravoltDir = $vendorDir . '/laravolt/laravolt';

        if (!is_dir($laravoltDir)) {
            return;
        }

        // Extract icons.zip to resources/icons
        self::extractFile(
            $laravoltDir . '/resources/icons.zip',
            getcwd() . '/resources/icons'
        );

        // Extract assets.zip to public/laravolt
        self::extractFile(
            $laravoltDir . '/resources/assets.zip',
            getcwd() . '/public/laravolt'
        );
    }

    /**
     * Extract a ZIP file to the specified destination.
     *
     * @param string $zipPath
     * @param string $destination
     * @return bool
     */
    private static function extractFile(string $zipPath, string $destination): bool
    {
        if (!file_exists($zipPath) || !class_exists('\ZipArchive')) {
            return false;
        }

        // Don't overwrite if destination already has files
        if (self::hasFiles($destination)) {
            return false;
        }

        // Create destination directory if it doesn't exist
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $zip = new \ZipArchive();
        $result = $zip->open($zipPath);

        if ($result !== TRUE) {
            return false;
        }

        $extracted = $zip->extractTo($destination);
        $zip->close();

        return $extracted;
    }

    /**
     * Check if the current directory is a Laravel project.
     *
     * @return bool
     */
    private static function isLaravelProject(): bool
    {
        return file_exists(getcwd() . '/artisan') &&
               file_exists(getcwd() . '/composer.json');
    }

    /**
     * Get the vendor directory path.
     *
     * @return string
     */
    private static function getVendorDir(): string
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        return dirname(dirname($reflection->getFileName()));
    }

    /**
     * Check if a directory has files or subdirectories.
     *
     * @param string $directory
     * @return bool
     */
    private static function hasFiles(string $directory): bool
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);
        return count($files) > 0;
    }
}
