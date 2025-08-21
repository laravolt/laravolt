<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileuploaderMediaHandler
{
    public function __invoke()
    {
        $action = request('_action');

        return $this->{$action}();
    }

    protected function upload()
    {
        /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
        $user = auth()->user() ?? Guest::first();

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

            report($e);
        } finally {
            return response()->json($response);
        }
    }

    protected function delete()
    {
        /** @var Model */
        $model = config('media-library.media_model');
        /** @var Media */
        $media = $model::query()->findOrfail(request('id'));
        $media->delete();

        return response()->json(true);
    }
}
