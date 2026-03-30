<?php

declare(strict_types=1);

namespace Laravolt\Media\Upload;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

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

    public function __construct(Request $request)
    {
        $rawIdentifier = $request->input('resumableIdentifier', $request->input('identifier', ''));
        $this->identifier = $this->sanitizeIdentifier($rawIdentifier);
        $this->chunkNumber = (int) $request->input('resumableChunkNumber', $request->input('chunkNumber', 1));
        $this->totalChunks = max(1, (int) $request->input('resumableTotalChunks', $request->input('totalChunks', 1)));
        $this->filename = $request->input('resumableFilename', $request->input('filename', 'upload'));
        $this->chunksDirectory = storage_path('app/chunks/'.$this->identifier);
    }

    /**
     * Save the uploaded chunk to temporary storage.
     */
    public function saveChunk(UploadedFile $file): void
    {
        if (! is_dir($this->chunksDirectory)) {
            mkdir($this->chunksDirectory, 0750, true);
        }

        $file->move($this->chunksDirectory, (string) $this->chunkNumber);
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
        $finalPath = storage_path('app/chunks/'.$this->identifier.'_assembled_'.$safeFilename);

        $destination = fopen($finalPath, 'wb');

        for ($i = 1; $i <= $this->totalChunks; $i++) {
            $chunkPath = $this->chunksDirectory.'/'.$i;
            $chunk = fopen($chunkPath, 'rb');
            stream_copy_to_stream($chunk, $destination);
            fclose($chunk);
            @unlink($chunkPath);
        }

        fclose($destination);
        @rmdir($this->chunksDirectory);

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
