<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Laravolt\Media\Upload\ResumableHandler;
use Laravolt\Platform\Models\Guest;
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
            if (! request()->hasFile('file') || ! request()->file('file')->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload missing file exception',
                ], 400);
            }

            $handler = new ResumableHandler(request());
            $handler->saveChunk(request()->file('file'));

            if ($handler->isComplete()) {
                $assembledPath = $handler->assembleChunks();
                $rawFilename = request()->input('resumableFilename', request()->input('filename', 'upload'));
                $filename = basename($rawFilename);

                $uploadedFile = new UploadedFile(
                    $assembledPath,
                    $filename,
                    null,
                    null,
                    true
                );

                try {
                    return $this->saveFile($uploadedFile);
                } catch (Exception $e) {
                    @unlink($assembledPath);
                    throw $e;
                }
            }

            return response()->json([
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        $handler = new ResumableHandler(request());

        if ($handler->chunkExists()) {
            return response()->json([
                'status' => 'partial',
                'percentage' => $handler->getPercentageDone(),
            ]);
        }

        // Return 204 so Resumable.js knows the chunk is missing and needs to be sent
        return response()->json([
            'status' => 'not_found',
            'percentage' => 0,
        ], 204);
    }
}
