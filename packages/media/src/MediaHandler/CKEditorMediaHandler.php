<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CKEditorMediaHandler
{
    public function __invoke()
    {
        $user = auth()->user() ?? Guest::first();

        try {
            $file = request('upload');
            $json = [];
            $i = 1;

            /** @var Media */
            $media = $user->addMedia($file)->toMediaCollection();
            $json["file-$i"] = [
                'url' => $media->getUrl(),
                'id' => $media->getKey(),
            ];

            return response()->json([
                'url' => $media->getUrl(),
                'uploaded' => 1,
                'fileName' => $media->file_name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'No file uploaded'
                ]
            ]);
        }
    }
}
