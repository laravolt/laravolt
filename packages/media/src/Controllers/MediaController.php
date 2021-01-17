<?php

namespace Laravolt\Media\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Media\MediaHandler\FileuploaderMediaHandler;
use Laravolt\Media\MediaHandler\RedactorMediaHandler;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function store()
    {
        switch (request('handler')) {
            case 'fileuploader':
                $handler = new FileuploaderMediaHandler();
                break;
            case 'redactor':
            default:
                $handler = new RedactorMediaHandler();
                break;
        }

        return $handler();
    }

    public function destroy($id)
    {
        Media::findOrfail($id)->delete();

        return redirect()->back()->withSuccess(__('Media deleted'));
    }
}
