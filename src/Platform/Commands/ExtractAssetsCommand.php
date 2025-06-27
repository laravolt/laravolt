<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class ExtractAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:extract-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract ZIP assets from Laravolt resources folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Extracting Laravolt assets...');

        // Extract icons.zip to resources/icons
        $this->extractFile(
            \Laravolt\platform_path('resources/icons.zip'),
            base_path('resources/icons'),
            'icons'
        );

        // Extract assets.zip to public/laravolt
        $this->extractFile(
            \Laravolt\platform_path('resources/assets.zip'),
            public_path('laravolt'),
            'public assets'
        );

        $this->info('Asset extraction completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Extract a ZIP file to the specified destination.
     *
     * @param string $zipPath Path to the ZIP file
     * @param string $destination Destination directory
     * @param string $description Description for user feedback
     * @return bool
     */
    protected function extractFile(string $zipPath, string $destination, string $description): bool
    {
        if (!file_exists($zipPath)) {
            $this->warn("ZIP file not found: {$zipPath}");
            return false;
        }

        // Check if destination already has files
        if (file_exists($destination) && $this->hasFiles($destination)) {
            $this->info("Skipped extraction of {$description}.");
            return false;
        }

        // Create destination directory if it doesn't exist
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $zip = new ZipArchive();
        $result = $zip->open($zipPath);

        if ($result !== TRUE) {
            $this->error("Failed to open ZIP file: {$zipPath}. Error code: {$result}");
            return false;
        }

        // Extract files
        if (!$zip->extractTo($destination)) {
            $this->error("Failed to extract {$description} to {$destination}");
            $zip->close();
            return false;
        }

        $zip->close();
        $this->info("Extracted {$description} to: {$destination}");

        return true;
    }

    /**
     * Check if a directory has files or subdirectories.
     *
     * @param string $directory
     * @return bool
     */
    protected function hasFiles(string $directory): bool
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);
        return count($files) > 0;
    }
}
