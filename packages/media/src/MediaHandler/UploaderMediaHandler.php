<?php

declare(strict_types=1);

namespace Laravolt\Media\MediaHandler;

use Laravolt\Platform\Services\FileUploader;

class UploaderMediaHandler
{
    public function __invoke()
    {
        FileUploader::handle(request('_key'));
        $response = ['isSuccess' => true];

        return response()->json($response);
    }
}
