<?php

declare(strict_types=1);

namespace Laravolt\Platform\Scripts;

use Composer\Script\Event;
use ReflectionClass;
use ZipArchive;

class ComposerScripts
{
    /**
     * Handle post-install and post-update events.
     */
    public static function postAutoloadDump(Event $event): void
    {
        // Only run if we're in a Laravel project
        if (! self::isLaravelProject()) {
            return;
        }

        self::extractAssets();
    }

    /**
     * Extract Laravolt assets to the appropriate directories.
     */
    public static function extractAssets(): void
    {
        $vendorDir = self::getVendorDir();
        $laravoltDir = $vendorDir.'/laravolt/laravolt';

        if (! is_dir($laravoltDir)) {
            return;
        }

        // Extract icons.zip to resources/icons
        self::extractFile(
            $laravoltDir.'/resources/icons.zip',
            \Laravolt\platform_path('resources'),
            'icons'
        );

        // Extract assets.zip to public/laravolt
        self::extractFile(
            $laravoltDir.'/resources/assets.zip',
            \Laravolt\platform_path(''),
            'public assets'
        );
    }

    /**
     * Extract a ZIP file to the specified destination.
     */
    private static function extractFile(string $zipPath, string $destination, string $description): bool
    {
        if (! file_exists($zipPath) || ! class_exists('\ZipArchive')) {
            return false;
        }

        $isIcons = $description === 'icons';
        $path = $isIcons ? 'icons' : 'public';
        if (is_dir($destination.DIRECTORY_SEPARATOR.$path)) {
            return false;
        }

        // Create destination directory if it doesn't exist
        if (! file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $zip = new ZipArchive;
        $result = $zip->open($zipPath);

        if ($result !== true) {
            return false;
        }

        $extracted = $zip->extractTo($destination);
        $zip->close();

        return $extracted;
    }

    /**
     * Check if the current directory is a Laravel project.
     */
    private static function isLaravelProject(): bool
    {
        return file_exists(getcwd().'/artisan') &&
               file_exists(getcwd().'/composer.json');
    }

    /**
     * Get the vendor directory path.
     */
    private static function getVendorDir(): string
    {
        $reflection = new ReflectionClass(\Composer\Autoload\ClassLoader::class);

        return dirname(dirname($reflection->getFileName()));
    }

    /**
     * Check if a directory has files or subdirectories.
     */
    private static function hasFiles(string $directory): bool
    {
        if (! is_dir($directory)) {
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        return count($files) > 0;
    }
}
