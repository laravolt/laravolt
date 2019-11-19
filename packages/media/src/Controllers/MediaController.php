<?php

namespace Laravolt\Media\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Media\MediaHandler\FileuploaderMediaHandler;
use Laravolt\Media\MediaHandler\RedactorMediaHandler;

class MediaController extends Controller
{
    public function store()
    {
        switch (request('handler')) {
            case 'fileuploader':
                $handler = new FileuploaderMediaHandler();
                break;
            case 'redactor':
                $handler = new RedactorMediaHandler();
                break;
            default:
                return response()->json(['error' => 'Invalid handler '.request('handler')], 400);
                break;
        }

        return $handler();
    }
}
