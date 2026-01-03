<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravolt\Platform\Models\Guest;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ChunkedMediaHandler
{
    public function __invoke(): JsonResponse
    {
        $action = request('_action', 'upload');

        return $this->{$action}();
    }

    /**
     * Handle chunk upload
     */
    protected function upload(): JsonResponse
    {
        try {
            // Create the file receiver
            $receiver = new FileReceiver('file', request(), HandlerFactory::classFromRequest(request()));

            // Check if the upload is successful, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }

            // Receive the file
            $save = $receiver->receive();

            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                // Save the file and return any response you need, current example uses `move` function.
                return $this->saveFile($save->getFile());
            }

            // We are in chunk mode, lets send the current progress
            /** @var AbstractHandler $handler */
            $handler = $save->handler();

            return response()->json([
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (UploadMissingFileException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload missing file exception',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save the final assembled file to media library
     */
    protected function saveFile($file): JsonResponse
    {
        try {
            /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
            $user = auth()->user() ?? Guest::first();

            // Add the file to media collection
            $media = $user->addMedia($file->getPathname())
                ->usingName($file->getClientOriginalName())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection();

            // Clean up the temporary file
            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }

            $response = [
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
            ];

            return response()->json($response);
        } catch (FileCannotBeAdded $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            report($e);

            return response()->json($response, 422);
        }
    }

    /**
     * Delete media file
     */
    protected function delete(): JsonResponse
    {
        try {
            /** @var Model */
            $model = config('media-library.media_model');
            /** @var Media */
            $media = $model::query()->findOrFail(request('id'));
            $media->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Check upload status - useful for resumable uploads
     */
    protected function status(): JsonResponse
    {
        try {
            $receiver = new FileReceiver('file', request(), HandlerFactory::classFromRequest(request()));

            // Check if we have an existing upload in progress
            $handler = $receiver->receive()->handler();

            return response()->json([
                'status' => 'partial',
                'percentage' => $handler->getPercentageDone(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'not_found',
                'percentage' => 0,
            ]);
        }
    }
}