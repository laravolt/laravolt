<?php

declare(strict_types=1);

namespace Laravolt\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupStaleChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300;

    /**
     * Hours after which chunks are considered stale
     */
    protected int $staleAfterHours;

    /**
     * Create a new job instance.
     */
    public function __construct(int $staleAfterHours = 24)
    {
        $this->staleAfterHours = $staleAfterHours;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->cleanupLocalChunks();
            $this->cleanupStorageChunks();
            
            Log::info('Chunked upload cleanup completed', [
                'stale_after_hours' => $this->staleAfterHours,
            ]);
        } catch (\Exception $e) {
            Log::error('Chunked upload cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Clean up stale chunks from local storage
     */
    protected function cleanupLocalChunks(): void
    {
        $chunksPath = storage_path('app/chunks');
        
        if (!File::exists($chunksPath)) {
            return;
        }

        $staleTime = now()->subHours($this->staleAfterHours);
        $deletedCount = 0;
        $totalSize = 0;

        // Get all chunk directories
        $chunkDirs = File::directories($chunksPath);

        foreach ($chunkDirs as $chunkDir) {
            $lastModified = File::lastModified($chunkDir);
            
            if ($lastModified < $staleTime->timestamp) {
                // Calculate size before deletion
                $dirSize = $this->getDirectorySize($chunkDir);
                $totalSize += $dirSize;
                
                // Delete the entire chunk directory
                File::deleteDirectory($chunkDir);
                $deletedCount++;
                
                Log::debug('Deleted stale chunk directory', [
                    'path' => $chunkDir,
                    'size' => $dirSize,
                    'last_modified' => date('Y-m-d H:i:s', $lastModified),
                ]);
            }
        }

        if ($deletedCount > 0) {
            Log::info('Local chunks cleanup completed', [
                'deleted_directories' => $deletedCount,
                'freed_space_bytes' => $totalSize,
                'freed_space_mb' => round($totalSize / 1024 / 1024, 2),
            ]);
        }
    }

    /**
     * Clean up stale chunks from configured storage disk
     */
    protected function cleanupStorageChunks(): void
    {
        $disk = Storage::disk(config('filesystems.default'));
        $chunksPath = 'chunks';
        
        if (!$disk->exists($chunksPath)) {
            return;
        }

        $staleTime = now()->subHours($this->staleAfterHours);
        $deletedCount = 0;

        // Get all chunk directories
        $chunkDirs = $disk->directories($chunksPath);

        foreach ($chunkDirs as $chunkDir) {
            try {
                $lastModified = $disk->lastModified($chunkDir);
                
                if ($lastModified < $staleTime->timestamp) {
                    // Delete the entire chunk directory
                    $disk->deleteDirectory($chunkDir);
                    $deletedCount++;
                    
                    Log::debug('Deleted stale chunk directory from storage', [
                        'path' => $chunkDir,
                        'last_modified' => date('Y-m-d H:i:s', $lastModified),
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to process chunk directory', [
                    'path' => $chunkDir,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($deletedCount > 0) {
            Log::info('Storage chunks cleanup completed', [
                'deleted_directories' => $deletedCount,
            ]);
        }
    }

    /**
     * Calculate directory size recursively
     */
    protected function getDirectorySize(string $directory): int
    {
        $size = 0;
        
        if (!File::exists($directory)) {
            return 0;
        }

        foreach (File::allFiles($directory) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Chunked upload cleanup job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}