<?php

declare(strict_types=1);

namespace Laravolt\Media\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Media\MediaHandler\ChunkedMediaHandler;
use Laravolt\Media\MediaHandler\FileuploaderMediaHandler;
use Laravolt\Media\MediaHandler\RedactorMediaHandler;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function store()
    {
        switch (request('handler')) {
            case 'chunked':
                $handler = new ChunkedMediaHandler;
                break;
            case 'fileuploader':
                $handler = new FileuploaderMediaHandler;
                break;
            case 'redactor':
            default:
                $handler = new RedactorMediaHandler;
                break;
        }

        return $handler();
    }

    public function destroy($id)
    {
        /** @var \Illuminate\Database\Eloquent\Model */
        $model = config('media-library.media_model');
        /** @var Media */
        $media = $model::query()->findOrfail(request('id'));
        $media->delete();

        return redirect()->back()->withSuccess(__('Media deleted'));
    }
}
