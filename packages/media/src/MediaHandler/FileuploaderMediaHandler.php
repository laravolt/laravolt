<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Laravolt\Platform\Models\Guest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class FileuploaderMediaHandler
{
    public function __invoke()
    {
        $req = request();
        $action = $req->input('_action', $req->query('_action'));

        // Infer action when missing: if any file fields exist, treat as upload; if id present without file, treat as delete
        if (!$action) {
            if (count($req->allFiles()) > 0) {
                $action = 'upload';
            } elseif ($req->filled('id')) {
                $action = 'delete';
            }
        }

        // Whitelist allowed actions to avoid calling unintended methods
        $allowed = ['upload', 'delete'];
        if (!$action || !in_array($action, $allowed, true) || !method_exists($this, $action)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid action',
            ], 400);
        }

        return $this->{$action}();
    }

    protected function upload()
    {
        /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
        $user = Auth::user() ?? Guest::query()->first();

        try {
            $media = $user->addMediaFromRequest(request('_key'))->toMediaCollection();

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
        } catch (FileCannotBeAdded $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            Log::error('File upload failed', ['exception' => $e]);
        } finally {
            return new JsonResponse($response);
        }
    }

    protected function delete()
    {
        /** @var Model */
        $model = Config::get('media-library.media_model');
        /** @var Media */
        $mediaId = request('id');
        $media = $model::query()->findOrFail($mediaId);
        $media->delete();

        return new JsonResponse(true);
    }
}
