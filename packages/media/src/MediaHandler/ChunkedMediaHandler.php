<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravolt\Platform\Models\Guest;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ChunkedMediaHandler
{
    protected Request $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?? request();
    }

    public function __invoke()
    {
        $action = $this->request->input('_action', 'upload');

        return $this->{$action}();
    }

    /**
     * Handle chunk upload
     */
    protected function upload(): JsonResponse
    {
        try {
            // Validate file if provided
            if ($this->request->hasFile('file')) {
                $this->validateFile($this->request->file('file'));
            }

            // Create file receiver
            $receiver = new FileReceiver($this->request->file('file'), $this->request, HandlerFactory::classFromRequest($this->request));

            // Check if upload is finished
            if ($receiver->isFinished()) {
                return $this->saveFile($receiver->receive());
            }

            // Handle chunk
            $handler = $receiver->handler();
            $content = $handler->getChunkContent();

            if ($content === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process chunk',
                ], 400);
            }

            // Save chunk
            $chunk = $handler->getChunkFile();
            $chunk->storeAs('chunks', $handler->getChunkFileName(), 'local');

            return response()->json([
                'success' => true,
                'done' => $handler->getPercentageDone(),
                'chunk' => $handler->getChunkIndex(),
                'total_chunks' => $handler->getTotalChunks(),
            ]);

        } catch (UploadFailedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 400);
        } catch (UploadMissingFileException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
            ], 400);
        } catch (\Exception $e) {
            report($e);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during upload',
            ], 500);
        }
    }

    /**
     * Complete upload and save to media library
     */
    protected function complete(): JsonResponse
    {
        try {
            $fileId = $this->request->input('file_id');
            $fileName = $this->request->input('file_name');
            
            if (!$fileId || !$fileName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing file_id or file_name',
                ], 400);
            }

            // Get the completed file
            $filePath = storage_path("app/chunks/{$fileId}/{$fileName}");
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                ], 404);
            }

            // Get user (authenticated or guest)
            /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
            $user = auth()->user() ?? Guest::first();

            // Add to media library
            $media = $user->addMedia($filePath)->toMediaCollection();

            // Clean up chunks
            $this->cleanupChunks($fileId);

            return response()->json([
                'success' => true,
                'files' => [
                    [
                        'file' => $media->getUrl(),
                        'name' => $media->file_name,
                        'size' => $media->size,
                        'type' => $media->mime_type,
                        'data' => [
                            'id' => $media->getKey(),
                            'url' => $media->getUrl(),
                            'thumbnail' => $media->getUrl(),
                        ],
                    ],
                ],
            ]);

        } catch (FileCannotBeAdded $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            report($e);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving file',
            ], 500);
        }
    }

    /**
     * Delete media
     */
    protected function delete(): JsonResponse
    {
        try {
            /** @var Model */
            $model = config('media-library.media_model');
            /** @var Media */
            $media = $model::query()->findOrFail($this->request->input('id'));
            $media->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            report($e);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media',
            ], 500);
        }
    }

    /**
     * Save completed file
     */
    protected function saveFile($file)
    {
        try {
            // Get user (authenticated or guest)
            /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
            $user = auth()->user() ?? Guest::first();

            // Add to media library
            $media = $user->addMedia($file->getPathname())->toMediaCollection();

            // Clean up temporary file
            unlink($file->getPathname());

            return response()->json([
                'success' => true,
                'files' => [
                    [
                        'file' => $media->getUrl(),
                        'name' => $media->file_name,
                        'size' => $media->size,
                        'type' => $media->mime_type,
                        'data' => [
                            'id' => $media->getKey(),
                            'url' => $media->getUrl(),
                            'thumbnail' => $media->getUrl(),
                        ],
                    ],
                ],
            ]);

        } catch (FileCannotBeAdded $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            report($e);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving file',
            ], 500);
        }
    }

    /**
     * Clean up chunk files
     */
    protected function cleanupChunks(string $fileId): void
    {
        try {
            $chunkPath = storage_path("app/chunks/{$fileId}");
            if (is_dir($chunkPath)) {
                $this->deleteDirectory($chunkPath);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            report($e);
        }
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
     * Validate uploaded file
     */
    protected function validateFile($file): void
    {
        // Check if validation is enabled
        if (!config('chunked-upload.security.validate_file_type', true) && 
            !config('chunked-upload.security.validate_file_size', true)) {
            return;
        }

        // Validate file size
        if (config('chunked-upload.security.validate_file_size', true)) {
            $maxSize = config('chunked-upload.max_file_size', 100 * 1024 * 1024);
            if ($file->getSize() > $maxSize) {
                throw new \Exception("File size exceeds maximum allowed size of " . $this->formatBytes($maxSize));
            }
        }

        // Validate file type
        if (config('chunked-upload.security.validate_file_type', true)) {
            $allowedTypes = config('chunked-upload.allowed_types', []);
            if (!empty($allowedTypes) && !in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception("File type '{$file->getMimeType()}' is not allowed");
            }
        }
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