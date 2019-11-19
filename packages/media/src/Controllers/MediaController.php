<?php

namespace Laravolt\Media\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Media\MediaHandler\RedactorMediaHandler;
use Laravolt\Media\MediaHandler\SingleFileUploaderMediaHandler;
use Laravolt\Media\MediaHandler\UploaderMediaHandler;

class MediaController extends Controller
{
    public function store()
    {
        switch (request('handler')) {
            case 'single-uploader':
                $handler = new SingleFileUploaderMediaHandler();
                break;
            case 'uploader':
                $handler = new UploaderMediaHandler();
                break;
            case 'redactor':
            default:
                $handler = new RedactorMediaHandler();
                break;
        }

        return $handler();
    }
}
