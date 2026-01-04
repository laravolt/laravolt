<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Exception;
use Laravolt\Platform\Models\Guest;

class RedactorMediaHandler
{
    public function __invoke()
    {
        $user = auth()->user() ?? Guest::first();

        try {
            $files = request('file');
            $json = [];
            $i = 1;

            foreach ($files as $file) {
                $media = $user->addMedia($file)->toMediaCollection();
                $json["file-$i"] = [
                    'url' => $media->getUrl(),
                    'id' => $media->getKey(),
                ];
                $i++;
            }

            return response()->json($json);
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}
