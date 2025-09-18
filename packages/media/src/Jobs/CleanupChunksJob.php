<?php

declare(strict_types=1);

namespace Laravolt\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CleanupChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $maxAgeHours;

    public function __construct(int $maxAgeHours = 24)
    {
        $this->maxAgeHours = $maxAgeHours;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $chunksPath = storage_path('app/chunks');
        
        if (!is_dir($chunksPath)) {
            return;
        }

        $this->cleanupDirectory($chunksPath);
    }

    /**
     * Recursively cleanup old chunk directories
     */
    protected function cleanupDirectory(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                // Check if directory is older than max age
                if ($this->isDirectoryOld($path)) {
                    $this->deleteDirectory($path);
                } else {
                    // Recursively check subdirectories
                    $this->cleanupDirectory($path);
                }
            }
        }
    }

    /**
     * Check if directory is older than max age
     */
    protected function isDirectoryOld(string $path): bool
    {
        $maxAge = $this->maxAgeHours * 3600; // Convert hours to seconds
        $directoryTime = filemtime($path);
        
        return (time() - $directoryTime) > $maxAge;
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
}