<?php

declare(strict_types=1);

namespace Laravolt\Media\Upload;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Laravolt\Media\ChunkedUploadConfig;

/**
 * Handles server-side chunked file uploads using the Resumable.js protocol.
 * Replaces pion/laravel-chunk-upload for Laravel 13+ compatibility.
 */
class ResumableHandler
{
    protected string $identifier;

    protected int $chunkNumber;

    protected int $totalChunks;

    protected string $filename;

    protected string $chunksDirectory;

    protected string $owner;

    protected int $declaredSize;

    public function __construct(Request $request)
    {
        $maxChunks = max(1, ChunkedUploadConfig::getMaxChunksPerFile());

        $rawIdentifier = $request->input('resumableIdentifier', $request->input('identifier', ''));
        $this->identifier = $this->sanitizeIdentifier($rawIdentifier);

        $totalChunks = max(1, (int) $request->input('resumableTotalChunks', $request->input('totalChunks', 1)));
        $this->totalChunks = min($totalChunks, $maxChunks);

        $chunkNumber = max(1, (int) $request->input('resumableChunkNumber', $request->input('chunkNumber', 1)));
        $this->chunkNumber = min($chunkNumber, $this->totalChunks);

        $this->filename = $request->input('resumableFilename', $request->input('filename', 'upload'));
        $this->declaredSize = max(0, (int) $request->input('resumableTotalSize', $request->input('totalSize', 0)));
        $this->owner = static::ownerOf($request);
        $this->chunksDirectory = storage_path('app/chunks/'.$this->owner.'/'.$this->identifier);
    }

    /**
     * Chunks live under a directory per uploader, so one uploader cannot add
     * chunks to, or complete, another's upload by reusing its identifier.
     */
    public static function ownerOf(Request $request): string
    {
        if (($id = $request->user()?->getAuthIdentifier()) !== null) {
            return 'user-'.preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string) $id);
        }

        $session = $request->hasSession() ? $request->session()->getId() : (string) $request->ip();

        return 'guest-'.substr(hash('sha256', $session), 0, 32);
    }

    /**
     * Save the uploaded chunk to temporary storage.
     */
    public function saveChunk(UploadedFile $file): void
    {
        $maxSize = ChunkedUploadConfig::getMaxFileSize();
        if ($maxSize !== null && $this->declaredSize > $maxSize) {
            throw new UploadLimitExceeded('The file is larger than the upload limit.', 413);
        }

        if (! is_dir($this->chunksDirectory)) {
            $this->guardPendingUploads();
            mkdir($this->chunksDirectory, 0750, true);
        }

        $file->move($this->chunksDirectory, (string) $this->chunkNumber);

        // The declared size comes from the client: check what is on disk too.
        if ($maxSize !== null && $this->storedBytes() > $maxSize) {
            $this->discard();

            throw new UploadLimitExceeded('The file is larger than the upload limit.', 413);
        }
    }

    /**
     * Remove every chunk of this upload.
     */
    public function discard(): void
    {
        foreach (glob($this->chunksDirectory.'/*') ?: [] as $chunk) {
            @unlink($chunk);
        }
        @rmdir($this->chunksDirectory);
    }

    protected function guardPendingUploads(): void
    {
        $pending = glob(dirname($this->chunksDirectory).'/*', GLOB_ONLYDIR) ?: [];
        if (count($pending) >= max(1, ChunkedUploadConfig::getMaxPendingUploads())) {
            throw new UploadLimitExceeded('Too many unfinished uploads. Finish or cancel one first.', 429);
        }
    }

    protected function storedBytes(): int
    {
        $bytes = 0;
        foreach (glob($this->chunksDirectory.'/*') ?: [] as $chunk) {
            $bytes += (int) filesize($chunk);
        }

        return $bytes;
    }

    /**
     * Check whether the current chunk has already been stored.
     */
    public function chunkExists(): bool
    {
        return file_exists($this->chunksDirectory.'/'.$this->chunkNumber);
    }

    /**
     * Check whether all chunks have been received.
     */
    public function isComplete(): bool
    {
        for ($i = 1; $i <= $this->totalChunks; $i++) {
            if (! file_exists($this->chunksDirectory.'/'.$i)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assemble all chunks into a single temporary file and return its path.
     * Cleans up individual chunk files and the chunk directory after assembly.
     */
    public function assembleChunks(): string
    {
        $safeFilename = basename($this->filename);
        $finalPath = storage_path('app/chunks/'.$this->owner.'--'.$this->identifier.'_assembled_'.$safeFilename);

        $destination = fopen($finalPath, 'wb');
        if ($destination === false) {
            throw new \RuntimeException(sprintf('Unable to open destination file for writing: %s', $finalPath));
        }

        $assembled = false;

        try {
            for ($i = 1; $i <= $this->totalChunks; $i++) {
                $chunkPath = $this->chunksDirectory.'/'.$i;
                $chunk = fopen($chunkPath, 'rb');
                if ($chunk === false) {
                    throw new \RuntimeException(sprintf('Unable to open chunk file for reading: %s', $chunkPath));
                }

                $copied = stream_copy_to_stream($chunk, $destination);
                fclose($chunk);

                if ($copied === false) {
                    throw new \RuntimeException(sprintf('Failed to copy chunk from %s to %s', $chunkPath, $finalPath));
                }

                @unlink($chunkPath);
            }

            $assembled = true;
        } finally {
            if (is_resource($destination)) {
                fclose($destination);
            }

            if (! $assembled && file_exists($finalPath)) {
                @unlink($finalPath);
            }

            @rmdir($this->chunksDirectory);
        }
        return $finalPath;
    }

    /**
     * Calculate the percentage of chunks that have been received.
     */
    public function getPercentageDone(): float
    {
        $received = 0;
        for ($i = 1; $i <= $this->totalChunks; $i++) {
            if (file_exists($this->chunksDirectory.'/'.$i)) {
                $received++;
            }
        }

        return round(($received / $this->totalChunks) * 100, 2);
    }

    /**
     * Strip characters that are unsafe for use as a filesystem directory name.
     */
    protected function sanitizeIdentifier(string $identifier): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $identifier) ?: 'upload';
    }
}
