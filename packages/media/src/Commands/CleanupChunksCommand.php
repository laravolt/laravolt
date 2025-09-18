<?php

declare(strict_types=1);

namespace Laravolt\Media\Commands;

use Illuminate\Console\Command;
use Laravolt\Media\Jobs\CleanupChunksJob;

class CleanupChunksCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'media:cleanup-chunks 
                            {--max-age=24 : Maximum age of chunks in hours}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old chunk files from chunked uploads';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $maxAge = (int) $this->option('max-age');
        $dryRun = $this->option('dry-run');

        $this->info("Cleaning up chunks older than {$maxAge} hours...");

        if ($dryRun) {
            $this->info('DRY RUN MODE - No files will be deleted');
            $this->performCleanup($maxAge, true);
        } else {
            $this->performCleanup($maxAge, false);
        }

        return Command::SUCCESS;
    }

    /**
     * Perform the cleanup operation
     */
    protected function performCleanup(int $maxAgeHours, bool $dryRun): void
    {
        $chunksPath = storage_path('app/chunks');
        
        if (!is_dir($chunksPath)) {
            $this->info('No chunks directory found.');
            return;
        }

        $maxAge = $maxAgeHours * 3600; // Convert hours to seconds
        $deletedCount = 0;
        $deletedSize = 0;

        $this->cleanupDirectory($chunksPath, $maxAge, $dryRun, $deletedCount, $deletedSize);

        if ($dryRun) {
            $this->info("Would delete {$deletedCount} files/directories");
            $this->info("Would free up " . $this->formatBytes($deletedSize) . " of space");
        } else {
            $this->info("Deleted {$deletedCount} files/directories");
            $this->info("Freed up " . $this->formatBytes($deletedSize) . " of space");
        }
    }

    /**
     * Recursively cleanup directory
     */
    protected function cleanupDirectory(string $dir, int $maxAge, bool $dryRun, int &$deletedCount, int &$deletedSize): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                // Check if directory is older than max age
                if ($this->isDirectoryOld($path, $maxAge)) {
                    $size = $this->getDirectorySize($path);
                    $deletedSize += $size;
                    $deletedCount++;
                    
                    if ($dryRun) {
                        $this->line("Would delete directory: {$path} (" . $this->formatBytes($size) . ")");
                    } else {
                        $this->deleteDirectory($path);
                        $this->line("Deleted directory: {$path} (" . $this->formatBytes($size) . ")");
                    }
                } else {
                    // Recursively check subdirectories
                    $this->cleanupDirectory($path, $maxAge, $dryRun, $deletedCount, $deletedSize);
                }
            }
        }
    }

    /**
     * Check if directory is older than max age
     */
    protected function isDirectoryOld(string $path, int $maxAge): bool
    {
        $directoryTime = filemtime($path);
        return (time() - $directoryTime) > $maxAge;
    }

    /**
     * Get directory size recursively
     */
    protected function getDirectorySize(string $dir): int
    {
        $size = 0;
        
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    $size += $this->getDirectorySize($path);
                } else {
                    $size += filesize($path);
                }
            }
        }
        
        return $size;
    }

    /**
     * Recursively delete directory
     */
    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}