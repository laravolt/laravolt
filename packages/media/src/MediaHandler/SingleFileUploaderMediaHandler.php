<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Laravolt\Platform\Services\SingleFileUploader;

class SingleFileUploaderMediaHandler
{
    public function __invoke()
    {
        $media = SingleFileUploader::handle(request('_key'));
        $response = [
            'isSuccess' => true,
            'files' => [
                [
                    'file' => $media->getFullUrl(),
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'type' => $media->mime_type,
                    'uploaded' => true,
                    "data" => [
                        'id' => $media->getKey(),
                        "url" => $media->getFullUrl(),
                        "thumbnail" => $media->getFullUrl(),
                        "readerForce" => true,
                    ],
                ],
            ],
        ];

        return response()->json($response);
    }
}
