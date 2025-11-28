<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class PresignedUrlMediaHandler
{
    public function __invoke()
    {
        $action = request('_action');

        if (method_exists($this, $action)) {
            return $this->{$action}();
        }

        return response()->json(['error' => 'Invalid action'], 400);
    }

    protected function get_presigned_url(): JsonResponse
    {
        $filename = request('filename');
        $uuid = (string) Str::uuid();
        $key = "tmp/{$uuid}/{$filename}";

        $diskName = config('media-library.disk_name');

        $expiration = now()->addMinutes(20);

        $options = [
            'ResponseContentType' => request('content_type', 'application/octet-stream'),
        ];

        try {
            $url = Storage::disk($diskName)->temporaryUploadUrl($key, $expiration, $options);

            return response()->json([
                'method' => 'PUT',
                'url' => $url,
                'headers' => [
                    'Content-Type' => request('content_type', 'application/octet-stream'),
                ],
                'key' => $key,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function save(): JsonResponse
    {
        $key = request('key');
        $filename = request('filename');
        $diskName = config('media-library.disk_name');

        if (! $key || ! $filename) {
            return response()->json(['error' => 'Missing key or filename'], 400);
        }

        /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
        $user = auth()->user() ?? Guest::first();

        try {
            $media = $user->addMediaFromDisk($key, $diskName)
                ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                ->usingFileName($filename)
                ->toMediaCollection();

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
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
