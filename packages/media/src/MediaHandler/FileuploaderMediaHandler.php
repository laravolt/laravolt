<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Laravolt\Platform\Models\Guest;

class FileuploaderMediaHandler
{
    public function __invoke()
    {
        $action = request('_action');

        return $this->{$action}();
    }

    protected function upload()
    {
        $user = auth()->user() ?? Guest::first();
        $media = $user->addMediaFromRequest(request('_key'))->toMediaCollection();
        $response = [
            'isSuccess' => true,
            'files' => [
                [
                    'file' => $media->getUrl(),
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'type' => $media->mime_type,
                    'uploaded' => true,
                    'data' => [
                        'id' => $media->getKey(),
                        'url' => $media->getUrl(),
                        'thumbnail' => $media->getUrl(),
                        'readerForce' => true,
                    ],
                ],
            ],
        ];

        return response()->json($response);
    }

    protected function delete()
    {
        $media = Media::find(request('id'));
        if ($media) {
            $media->delete();
        }

        return response()->json(true);
    }
}
